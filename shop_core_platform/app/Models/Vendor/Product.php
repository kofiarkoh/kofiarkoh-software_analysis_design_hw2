<?php

namespace App\Models\Vendor;

use App\Models\OrderItem;
use App\Models\School;
use App\Models\Shop;
use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\QueryBuilder\QueryBuilder;

#[ObservedBy([ProductObserver::class])]
class Product extends Model
{
    use SoftDeletes, LogsActivity, Searchable;

    protected $guarded = false;

    protected $casts = [
        'photos' => 'array',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUSPENDED = 'suspending';
    public const STATUS_PUBLISHED = 'published';

    public const VENDOR_STATUSES = [
        self::STATUS_DRAFT     => 'Draft',
        self::STATUS_PUBLISHED  => 'Published',
    ];

    public const ADMIN_STATUSES = [

        self::STATUS_PUBLISHED  => 'Published',
        self::STATUS_SUSPENDED  => 'Suspended',
    ];

    public function toSearchableArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => (float) $this->price,
            'status'      => $this->status,            // for filters
            'quantity'    => (int) $this->quantity,    // for filters
        ];
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty() ;
    }
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }


    public static function baseFilter()
    {
        return QueryBuilder::for(self::class)
            ->allowedFilters(['name', 'categories.slug'])
            ->where('quantity', '>', 0)
            ->whereHas('shop', fn ($q) =>
            $q->whereNotIn('status', [Shop::STATUS_SUSPENDED, Shop::STATUS_UNDER_REVIEW])
            )
            ->whereHas('shop.owner', fn ($q) =>
            $q->whereNotIn('status', [Vendor::STATUS_SUSPENDED, Vendor::STATUS_UNDER_REVIEW])
            )
            ->where('status', self::STATUS_PUBLISHED);
    }

    public static function filtered( ?int $shopId = null)
    {
        return self::baseFilter()
            ->when($shopId, fn (Builder $query) => $query->where('shop_id', $shopId));
    }
    public static function filteredPaginated( int $perPage = 15, ?int $shopId = null)
    {
        return self::baseFilter()
            ->when($shopId, fn (Builder $query) => $query->where('shop_id', $shopId))
            ->paginate($perPage);
    }


    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStockAttribute(): int
    {
        if ($this->variants()->exists()) {
            return $this->variants()->sum('stock');
        }
        return $this->quantity;
    }

    public function scopeNewArrivals($q)
    {
        // last 30 days
        return $q->where('created_at', '>=', now()->subDays(30))->latest();
    }

    /**
     * Get similar products (filtered via baseFilter) ordered by ML score.
     *
     * @param  int   $limit
     * @param  bool  $sameShop  If true, restrict to this product's shop_id
     * @return \Illuminate\Support\Collection<static>
     */
    public function similarFromBaseFilter(int $limit = 12, bool $sameShop = false): Collection
    {
        // NOTE: baseFilter() already applies stock/shop/vendor/status constraints
        return self::baseFilter()
            ->select('products.*', 'sp.score')
            ->join('similar_products as sp', 'sp.similar_id', '=', 'products.id')
            ->where('sp.product_id', $this->id)
            ->when($sameShop, fn ($q) => $q->where('products.shop_id', $this->shop_id))
            ->where('products.id', '!=', $this->id) // safety, in case self-pairs ever exist
            ->orderByDesc('sp.score')
            ->limit($limit)
            ->get();
    }
}
