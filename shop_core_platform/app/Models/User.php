<?php

namespace App\Models;

use App\Traits\VerifiesPhoneNumber;
use Filament\Models\Contracts\HasName;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, VerifiesPhoneNumber;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // in Admin model
    public function getFilamentName(): string
    {
        return $this->first_name;
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function searchQueries(): HasMany
    {
        return $this->hasMany(SearchQuery::class);
    }
    public function customerAddresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function activeCart(): HasOne
    {
        return $this->hasOne(Cart::class)->where('status', 'active');
    }

    public function cartItemCount(): int
    {
        $cart = $this->activeCart;

        if (! $cart) {
            return 0;
        }

        return $cart->items->sum('quantity');
    }

    public function cartTotalPrice(): float
    {
        $cart = $this->activeCart;

        if (! $cart) {
            return 0.00;
        }

        $total = $cart->items->sum(function ($item) {
            $price = $item->variant->price ?? $item->product->price ?? 0;
            return $item->quantity * $price;
        });

        return round($total, 2);
    }


    public function otpTokens(): MorphMany {
        return $this->morphMany(OtpToken::class, 'otpTokenable', 'otp_tokenable_type', 'otp_tokenable_id');
    }


}
