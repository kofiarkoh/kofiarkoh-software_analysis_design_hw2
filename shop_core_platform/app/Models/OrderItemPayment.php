<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemPayment extends Model
{
    protected $guarded = false;
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function transaction(): BelongsTo
    {

        return $this->belongsTo(Transaction::class);
    }


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
