<?php

namespace App\Models;

use App\Models\Vendor\Product;
use App\Models\Vendor\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $guarded = false;

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getProductStockAttribute(): int
    {
        if ($this->variant){
            return $this->variant->stock;
        }
        return $this->product->quantity ?? 0;
    }

}
