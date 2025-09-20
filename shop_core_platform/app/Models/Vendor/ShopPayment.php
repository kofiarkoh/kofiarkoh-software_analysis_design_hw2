<?php

namespace App\Models\Vendor;

use App\Models\OrderItem;
use App\Models\Shop;
use App\Models\Transaction;
use App\States\Transaction\SuccessTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopPayment extends Model
{
    use SoftDeletes;
    public const CREDIT_PAYMENT = 'credit'; // add money to the account
    public const DEBIT_PAYMENT = 'debit'; // withdraw money


    public const REASON_VENDOR_PAYOUT_REQUEST_REVERSED = 'vendor-payout-request-reversed';

    protected $guarded = false;


    public function getTransactionStatusAttribute()
    {
        if ($this->transaction()->exists()){
            return $this->transaction->status;
        }
        elseif($this->transactions()->exists()){
            return $this->transactions()->first()->status;
        }
        elseif (str_contains($this->reference, self::REASON_VENDOR_PAYOUT_REQUEST_REVERSED )) {
            return SuccessTransaction::$name;
        }
        return null;
    }
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function transaction(): MorphOne{
        return $this->morphOne(Transaction::class, 'transactable');
    }

    public function transactions(): MorphToMany
    {
        return $this->morphToMany(Transaction::class, 'transactable');
    }
}
