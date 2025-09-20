<?php

namespace App\Models;

use App\Models\Vendor\Attribute;
use App\Models\Vendor\AttributeValue;
use App\Models\Vendor\PayoutAccount;
use App\Models\Vendor\Product;
use App\Models\Vendor\ProductVariant;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\ShopPayment;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use SoftDeletes, Sluggable;
    protected $guarded = false;


    const STATUS_APPROVED = 'approved';
    const STATUS_UNDER_REVIEW = 'under-review';
    const STATUS_SUSPENDED = 'suspended';

    const STATUSES = [
        self::STATUS_UNDER_REVIEW => 'Under Review',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_SUSPENDED => 'Suspended',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Shop $shop) {
            if (empty($shop->slug)) {
                $shop->slug = $shop->generateUniqueSlug(self::class, (string) $shop->name);
            }
        });

        static::updating(function (Shop $shop) {
            if ($shop->isDirty('name') && (empty($shop->slug) || $shop->getOriginal('slug') === null)) {
                $shop->slug = $shop->generateUniqueSlug(self::class, (string) $shop->name);
            }
        });
    }
    public static function clearShopInformationFromSession(): void
    {
        session()->forget('shop_id');
        session()->forget('shop_name');
        session()->forget('shop_slug');
    }

    public static function setShopInformationInSession(string $slug): void
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();

        $shopId = $shop->id;

        session()->put('shop_id', $shopId);
        session()->put('shop_slug', $slug);
        session()->put('shop_name', $shop->name);
    }
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'owner_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'vendor_shop', 'shop_id', 'vendor_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'shop_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'shop_id', 'id');
    }


    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'shop_id', 'id');
    }


    public function payments(): HasMany {
        return $this->hasMany(ShopPayment::class, 'shop_id', 'id');
    }

    public function payoutAccounts(): HasMany {
        return $this->hasMany(PayoutAccount::class, 'shop_id', 'id');
    }

    public function balance(): float
    {
        $lastPayment = $this->payments()
            ->latest('created_at')
            ->orderByDesc('id')
            ->first();

        return $lastPayment? $lastPayment->balance : 0.00;
    }


    public function reversePayoutTransfer(Transaction $transaction, ShopPayment $payment){

        $balance = $this->balance();
        $this->payments()->create([
            'amount' =>  $transaction->amount,
            'balance' => $balance  + $transaction->amount,
            'payment_type' =>  ShopPayment::CREDIT_PAYMENT,
            'reference' => ShopPayment::REASON_VENDOR_PAYOUT_REQUEST_REVERSED . " for #" . $payment->id,
        ]);
    }

    public  function  getWebsiteURL() : string
    {
        return  route('shops.index', ['shop' => $this->slug]);
    }
}
