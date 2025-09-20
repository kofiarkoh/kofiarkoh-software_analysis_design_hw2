<?php

namespace App\Models\Vendor;

use App\Jobs\SendVendorOrderConfirmationSms;
use App\Models\OtpToken;
use App\Models\Shop;
use App\Utils\Arkesel;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Database\Factories\UserFactory;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class Vendor extends Authenticatable implements HasTenants, HasName, MustVerifyEmail
{
    use Notifiable;

    protected string $guard = 'vendor';
    protected $table = 'vendors';

    protected $guarded = false;

    const STATUS_APPROVED = 'approved';
    const STATUS_UNDER_REVIEW = 'under-review';
    const STATUS_SUSPENDED = 'suspended';

    const VENDOR_STATUSES = [
        self::STATUS_UNDER_REVIEW => 'Under Review',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_SUSPENDED => 'Suspended',
    ];
    const VENDOR_STATUS_DESCRIPTIONS = [
        self::STATUS_APPROVED => 'Your Account is Approved.',
        self::STATUS_UNDER_REVIEW => 'Under Review: You will able to manage set up products and but cannot receive orders until your account is approved.',
        self::STATUS_SUSPENDED => 'Yours account is suspended. Please contact support team for more information.',
    ];


    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }


    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'vendor') {
            $user = auth('filament')->user();
            return $user && $user->hasRole('vendor-admin');

        }

        elseif ($panel->getId() === 'admin') {
            $user = auth('filament')->user();
            return $user && $user->hasRole('super-admin');

        }

        return false;
    }

    public function currentTeam()
    {
        return Filament::getTenant();
    }


    public function canAccessTenant(Model $tenant): bool
    {
        return $this->shops->contains($tenant);
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->shops;
    }

    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'vendor_shop', 'vendor_id', 'shop_id');
    }

    public function otpTokens(): MorphMany {
        return $this->morphMany(OtpToken::class, 'otpTokenable', 'otp_tokenable_type', 'otp_tokenable_id');
    }


    public function sendOTPToken(): void
    {

        $token = $otp = random_int(100000, 999999);

        $this->otpTokens()->create([
            'token' => $token,
            'expires_at' => now()->addMinutes(10),
        ]);

        $arkesel = new Arkesel();

        $message = "Your one time verification token is {$token}";
        $arkesel->sendSMS($message, [$this->phone_number]);
    }

    public function getLatestUnexpiredOTPToken(): ?string
    {
        return $this->otpTokens()
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->value('token'); // returns the token string or null
    }

    public function sendVendorOrderConfirmationSMS(): void
    {
        $message = "Your have received a new order. Please login to your dashboard to review the order.";
        SendVendorOrderConfirmationSms::dispatch($this->phone_number, $message);
    }

}
