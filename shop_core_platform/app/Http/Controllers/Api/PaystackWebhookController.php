<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vendor\ShopPayment;
use App\Models\Vendor\Vendor;
use App\Notifications\OrderPaid;
use App\Notifications\VendorOrderReceived;
use App\States\Transaction\FailedTransaction;
use App\States\Transaction\PendingTransaction;
use App\States\Transaction\SuccessTransaction;
use App\Utils\Payment\PayStack;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {


        try {


            DB::beginTransaction();
            $event = $request['event'];

            $trans_id = $request['data']['reference'];
            $transStatus = $request['data']['status'];

            $transaction = Transaction::where('uuid',$trans_id)->firstOrFail();

            if ($transaction->status == SuccessTransaction::$name || $transaction->status == FailedTransaction::$name) {
                return response()->json("Transaction already completed");
            }

            if (in_array($event,  PayStack::TRANSFER_EVENTS)) {
                /** HANDLES PAYMENT RESPONSES RELATED TO PAYOUTS */

                if ($event == PayStack::TRANSFER_SUCCESS) {
                    $transaction->status->transitionTo(SuccessTransaction::class);
                    $transaction->paystack_final_response = $request->all();
                    $transaction->save();
                    DB::commit();
                    return response()->json("Transaction processed successfully");
                }
                elseif ($event == PayStack::TRANSFER_FAILED || $event == PayStack::TRANSFER_REVERSED) {
                    $transaction->status->transitionTo(FailedTransaction::class);
                    $transaction->paystack_final_response = $request->all();
                    $transaction->save();

                    /** @var ShopPayment $shopPayment */
                    $shopPayment =  $transaction->transactable;

                    /** @var Shop $shop */
                    $shop =  $shopPayment->shop;

                    $shop->reversePayoutTransfer($transaction, $shopPayment);
                    DB::commit();
                    return response()->json("Transaction processed successfully");
                }
            }

           else{

               // ORDER TRANSACTION PROCESSING

               /** @var User $user */
               $user = $transaction->user;

               /** @var Order $order */
               $order = $transaction->order;

               if (strtolower($transStatus) == 'success' && $transaction->status == PendingTransaction::$name ) {

                   $transaction->status->transitionTo(SuccessTransaction::class);

                   foreach ($order->items()->get() as $item) {
                       if ($item->payment) {
                         continue;
                       }

                       if($item->variant) {
                           $item->variant->update([
                               'reserved_stock' => max($item->variant->reserved_stock - $item->quantity, 0),
                           ]);
                       }else {
                           $item->product->update([
                               'reserved_stock' => max( $item->product->reserved_stock - $item->quantity, 0),
                           ]);
                       }

                       $item->payment()->create([
                           'order_id' => $order->id,
                           'transaction_id' => $transaction->id,
                       ]);

                       /** @var Shop $shop */
                       $shop = $item->shop;

                       /** @var ShopPayment $shopPayment */
                       $shopPayment =  $shop->payments()->create([
                           'amount' =>  $item->vendor_earnings,
                           'payment_type' => ShopPayment::CREDIT_PAYMENT,
                           'order_item_id' => $item->id,
                           'balance' => $shop->balance() + $item->vendor_earnings,
                       ]);

                       $shopPayment->transactions()->attach($transaction);


                   }
                   $itemsByShop = $order->items()
                       ->with('product.shop')
                       ->get()
                       ->groupBy(fn ($item) => $item->product->shop->id);

                   foreach ($itemsByShop as $shopId => $items){
                       $shop = Shop::where('id', $shopId)->first();

                       /** @var Vendor $vendor */
                       $vendor = $shop->owner;

                       $vendor->sendVendorOrderConfirmationSMS();
                       $vendor->notify(new VendorOrderReceived( $shop, $items));

                   }
                   $user->notify(new OrderPaid($order->items()->get()));
               } else {

                   // TRANSACTION FAILED
                   foreach ($order->items()->get() as $item) {
                       if($item->variant) {
                           $reservedStock = $item->variant->reserved_stock - $item->quantity;

                           $item->variant->update([
                               'reserved_stock' => max($reservedStock, 0),
                               'stock' => $item->variant->stock + $item->quantity,
                           ]);
                       }else {
                           $item->product->update([
                               $reservedStock = $item->product->reserved_stock - $item->quantity,

                               'reserved_stock' => max($reservedStock, 0) ,
                               'quantity' => $item->product->quantity + $item->quantity,
                           ]);
                       }
                   }

                   $transaction->status->transitionTo(FailedTransaction::class);
               }
                $transaction->save();
               DB::commit();
               return response()->json("Transaction processed successfully");
           }

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => "Transaction processing failed",
                'error' => $e->getMessage(),
            ]);
        }

    }
}
