<?php

namespace App\Http\Controllers;

use App\Models\Admin\DeliveryCity;
use App\Models\Admin\DeliveryRegion;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Payment\PayStack;
use App\Settings\AppSettings;
use App\States\Transaction\PendingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{

    public static function generateNumericOrderNumber(): string
    {
        do {
            // Combine last 5 digits of timestamp + 4-digit random
            $number = substr(now()->format('U'), -5) . random_int(1000, 9999);
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    public function selectAddress()
    {
        /** @var User $user */
        $user = Auth::user();
        $addresses = $user->customerAddresses;

        $regions = DeliveryRegion::with('cities')->get();
        return view('orders.checkout.address', compact('addresses', 'regions'));
    }

    public function confirmAddress(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:delivery_cities,id',
            'nearby_city' => 'nullable|string|max:255',
            'delivery_instructions' => 'nullable|string|max:1000',
        ],
            [],
            [
                'city_id' => 'City',
                'nearby_city' => 'Nearby City or Landmark',
                'delivery_instructions' => 'Delivery Instructions',
            ]
        );

        $city = DeliveryCity::where('id',$validated['city_id'])->firstOrFail();

        session([
            'checkout_city_id' => $city->id,
            'checkout_delivery_fee' => $city->delivery_fee,
            'checkout_nearby_city' => $validated['nearby_city'],
            'checkout_delivery_instructions' => $validated['delivery_instructions'],
        ]);

        return redirect()->route('checkout.payment.choose-method');
    }

    public function showPayment()
    {
        // Make sure an address was selected
        $addressId = session('checkout_city_id');

        if (!$addressId) {
            return redirect()->route('checkout.address')->with('error', 'No shipping address selected.');
        }

        $city = DeliveryCity::where('id', $addressId)->firstOrFail();
        $deliveryFee = $city->delivery_fee;
        $cartItems = auth()->user()->activeCart?->items ?? collect();
        $totalAmount = auth()->user()->cartTotalPrice() + $deliveryFee;

        return view('orders.checkout.payment', compact('city', 'cartItems', 'totalAmount', 'deliveryFee'));
    }

    public function processPayment(Request $request, AppSettings $appSettings)
    {
        if (
            !session()->has('checkout_city_id') ||
            !session()->has('checkout_delivery_fee')
        ) {
            return redirect()->route('cart.index')
            ->with('error', 'Session data is missing. Please try again.');
        }


        $request->validate([
            'payment_method' => 'required|in:mobile_money',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $cart = $user->activeCart;

            if (! $cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }

            $pickUpCityId = session('checkout_city_id');
            $pickUpCity = DeliveryCity::where('id', $pickUpCityId)->firstOrFail();
            if (! $pickUpCity) {
                return redirect()->route('checkout.address')->with('error', 'No shipping address selected.');
            }


            $totalPrice = $user->cartTotalPrice() + $pickUpCity->delivery_fee;
            $totalPrice = round($totalPrice, 2);

            $order = $user->orders()->create([
                'payment_method' => $request->payment_method,
                'total_price' => $totalPrice ,
                'cart_price' => $user->cartTotalPrice(),
                'delivery_fee' => $pickUpCity->delivery_fee,
                'status' => 'pending',
                'delivery_city_id' => $pickUpCity->id,
                'delivery_instructions' => session('checkout_delivery_instructions'),
                'nearby_city' => session('checkout_nearby_city'),
                'order_number' =>  self::generateNumericOrderNumber(),
            ]);

            $commissionRate = $appSettings->order_commission;

            foreach ($cart->items as $item) {
                if($item->product_variant_id) {

                    if ( $item->variant->stock < $item->quantity ) {
                        DB::rollBack();
                        return redirect()->route('cart.index')->with('error', 'Insufficient stock for '. $item->product->name);
                    }
                    $item->variant->update([
                        'reserved_stock' => $item->variant->reserved_stock + $item->quantity,
                        'stock' => $item->variant->stock - $item->quantity,
                        ]);
                }else {

                    if ( $item->product->quantity < $item->quantity ) {
                        DB::rollBack();
                        return redirect()->route('cart.index')->with('error', 'Insufficient stock for '. $item->product->name);
                    }
                    $item->product->update([
                        'reserved_stock' => $item->product->reserved_stock + $item->quantity,
                        'quantity' => $item->product->quantity - $item->quantity,
                        ]);
                }
                $unitPrice = $item->variant->price ?? $item->product->price;
                $itemsTotalPrice =  $item->quantity * $unitPrice;

                $orderCommission = ($commissionRate / 100) * $itemsTotalPrice;
                $orderCommission = round($orderCommission, 2);
                $vendorEarnings = $itemsTotalPrice - $orderCommission;



                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $unitPrice,
                    'total_price' => $itemsTotalPrice,
                    'shop_id' => $item->product->shop_id,
                    'order_commission_rate' => $commissionRate,
                    'order_commission' => $orderCommission,
                    'vendor_earnings' => $vendorEarnings,
                ]);
            }

            $cart->update(['status' => 'ordered']);

            $transactionId = (string) Str::uuid();

            $paymentData = [
                'amount' =>  $totalPrice,
                'transactionId' => $transactionId,
            ];

            $paymentResponse = \App\Utils\Payment\PayStack::generatePaymentRequestUrl($paymentData);
            $paymentURL = $paymentResponse['data']['authorization_url'];


            $transactionData = [
                'order_id' => $order->id,
                'user_id' => auth()->user()->id,
                'category' => Transaction::CATEGORY_ORDER_PAYMENT,
                'status' => (string) PendingTransaction::class,
                'amount' => $totalPrice,
                'uuid' => $transactionId ,
                'payment_url' => $paymentURL,
                'paystack_initial_response' =>  json_encode($paymentResponse),
            ];

            $order->transaction()->create($transactionData);

            DB::commit();

            session()->forget(['checkout_city_id' ,'checkout_delivery_instructions' , 'checkout_nearby_city']);;
            return redirect()->away($paymentURL);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }


    }

}
