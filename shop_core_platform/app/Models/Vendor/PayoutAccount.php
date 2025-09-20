<?php

namespace App\Models\Vendor;

use App\Models\Shop;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayoutAccount extends Model
{
    use SoftDeletes;

    protected $guarded = false;

    const MOMO_ACCOUNT = 'mobile_money';

    const ACCOUNT_OPTIONS = [
      self::MOMO_ACCOUNT => 'Mobile Money'
    ];
    const MOMO_NETWORK_OPTIONS = [
        'MTN' => 'MTN'
    ];


    const STATUS_REJECTED_BY_PAYSTACK = 'rejected_by_paystack';
    const STATUS_APPROVED_BY_PAYSTACK = 'approved_by_paystack';

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }
}
