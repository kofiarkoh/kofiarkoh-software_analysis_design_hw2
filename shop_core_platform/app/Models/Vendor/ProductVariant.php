<?php

namespace App\Models\Vendor;

use App\Models\Shop;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductVariant extends Model
{
    use LogsActivity, SoftDeletes;

    protected $guarded = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty() ;
    }
    protected static function booted(): void
    {
        static::creating(function ($value) {
            if (! $value->shop_id) {
                $value->shop_id = Filament::getTenant()?->id;
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attribute_value')
            ->with('attribute');
    }


    public function getAttributePairs(): array
    {
        return $this->attributeValues->mapWithKeys(fn ($value) => [
            $value->attribute->name => $value->value
        ])->toArray();
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }
}
