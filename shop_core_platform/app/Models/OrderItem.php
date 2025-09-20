<?php

namespace App\Models;

use App\Models\Vendor\Product;
use App\Models\Vendor\ProductVariant;
use App\States\Transaction\PendingTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'quantity',
        'price',
        'shop_id',
        'status',
        'order_commission_rate',
        'order_commission',
        'vendor_earnings',
        'total_price',
    ];

    /**
     * The order this item belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The product this item is for.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The product variant (if any) this item is for.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public function payment(): HasOne{

        return $this->hasOne(OrderItemPayment::class, 'order_item_id', 'id');
    }


    protected function getTransactionStatusAttribute() : string
    {
        return $this->payment ? $this->payment->transaction?->status : PendingTransaction::$name;
    }

}
