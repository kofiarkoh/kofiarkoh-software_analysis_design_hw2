<?php

namespace App\Models;

use App\States\TransactionState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Transaction extends Model
{
    use HasStates, SoftDeletes;

    protected $casts = [
        'status' => TransactionState::class,
    ];

    protected $guarded = false;


    const CATEGORY_ORDER_PAYMENT = "order-payment";
    const CATEGORY_VENDOR_PAYOUT = 'vendor-payout';


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class,  'order_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,  'user_id', 'id');
    }

    public function transactable(): MorphTo {
        return $this->morphTo();
    }
}
