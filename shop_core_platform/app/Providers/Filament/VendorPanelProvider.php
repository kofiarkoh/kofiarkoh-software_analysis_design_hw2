<?php

namespace App\Providers\Filament;

use App\Filament\Vendor\Pages\EditShopProfile;
use App\Filament\Vendor\Pages\RegisterShop;
use App\Filament\Vendor\Pages\RegisterVendor;
use App\Filament\Vendor\Widgets\ShopUrlWidget;
use App\Filament\Vendor\Widgets\ShopProductOrderingRateGraphWidget;
use App\Models\Shop;
use Chiiya\FilamentAccessControl\FilamentAccessControlPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
class VendorPanelProvider extends PanelProvider
{


    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('vendor')
            ->path('vendor')
            ->authGuard('vendor')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->tenant(model: Shop::class, ownershipRelationship: 'shop')
            ->databaseNotifications()
            ->tenantRegistration(RegisterShop::class)
            ->tenantProfile(EditShopProfile::class)
            ->registration(RegisterVendor::class)
            ->discoverResources(in: app_path('Filament/Vendor/Resources'), for: 'App\\Filament\\Vendor\\Resources')
            ->discoverPages(in: app_path('Filament/Vendor/Pages'), for: 'App\\Filament\\Vendor\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Vendor/Widgets'), for: 'App\\Filament\\Vendor\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->login()
            ->emailVerification()
            ->emailVerifiedMiddlewareName(EnsureEmailIsVerified::class)
            ->passwordReset()
            ->authMiddleware([
                Authenticate::class,
            ])
            ;
    }
}
