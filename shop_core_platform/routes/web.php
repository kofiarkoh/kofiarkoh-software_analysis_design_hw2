<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\UserProfileController;
use App\Http\Middleware\EnsurePhoneNumberIsVerified;
use App\Notifications\VendorOrderReceived;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// routes/web.php
use Illuminate\Support\Facades\Mail;

Route::get('/mail-test', function () {
    Mail::raw('Mail test body', function ($m) {
        $m->to('kofiarkoh0@gmail.com')->subject('Mail test');
    });
    return 'sent (check logs/inbox)';
});

Route::get('/search-suggestions', [ProductController::class, 'suggestions']);

Route::middleware([\App\Http\Middleware\EnsureNotInProduction::class])->group( function () {
    Route::get('/vendor-order-received-notification', function () {
        $shop = \App\Models\Shop::where('id', 1)->first();
        $items = \App\Models\Order::where('id', 56)->first()->items;
        return (new VendorOrderReceived($shop, $items))->toMail($shop->owner);
    });


    Route::get('/user-order-paid-notification', function () {
        $shop = \App\Models\Shop::where('id', 1)->first();
        $items = \App\Models\Order::where('id', 56)->first()->items;
        return (new \App\Notifications\OrderPaid($items))->toMail($shop->owner);
    });
});

Route::middleware([ \App\Http\Middleware\OptionalVerification::class])->group(function () {
    Route::get('/', [HomepageController::class, 'home'])->name('homepage');
    Route::get('/products', [ProductController::class, 'index'])->name("products.index");
    Route::get('/products/{product}', [ProductController::class, 'show'])->name("products.detail");


    Route::get('/shops/{shop}', [HomepageController::class, 'shopHomepage'])->name('shops.index');
    Route::get('/shops/{shop}/products', [ProductController::class, 'indexByShop'])->name('shops.products.index');

    Route::get('/shops/{shop}/products/{product}', [ProductController::class, 'showForShop'])->name('shops.products.show');

});


Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');


Route::prefix('auth')->name('auth.')->group(function () {

    Route::view('/register', 'auth.register')->name('register-page');
    Route::view('/login', 'auth.login')->name('login-page');

    Route::post('/register', RegisterController::class)->name("register");

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password-reset.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password-reset.email');


});
Route::post('/auth/login', LoginController::class)->name("login");



Route::middleware(['auth'])->group(function () {

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent. Please check your email.');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->intended('/')->with('success', 'Your email has been verified successfully.');

    })->middleware(['signed'])->name('verification.verify');



    /**  PHONE NUMBER VERIFICATION ROUTES START */

    Route::middleware(['auth'])->group(function () {
        Route::get('/verify-phone', [PhoneVerificationController::class, 'showNotice'])->name('verify.phone.notice');
        Route::post('/verify-phone-send-token', [PhoneVerificationController::class, 'sendToken'])->name('verify.phone.send-token');


        Route::get('/verify-phone-token', [PhoneVerificationController::class, 'showVerificationForm'])->name('verify.phone.verification-form');
        Route::post('/verify-phone', [PhoneVerificationController::class, 'verify'])->name('verify.phone.submit');

    });

    /**  PHONE NUMBER VERIFICATION ROUTES END */



    Route::middleware(['verified'])->group(function () {
        Route::view('/profile', 'user.profile')->name('user.profile');
        Route::get('/profile/orders', [UserProfileController::class, 'orders'])->name('user.orders');
        Route::get('/orders/{order}', [UserProfileController::class, 'showOrder'])->name('user.orders.show');

        Route::resource('cart', CartController::class)->except(['create', 'edit', 'update']);
        Route::resource('addresses', \App\Http\Controllers\CustomerAddressController::class);
        Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/item/{cartItem}', [CartController::class, 'destroyItem'])->name('cart.item.destroy');

        Route::post('/cart/bulk-add', [CartController::class, 'bulkAdd'])->name('cart.bulkAdd');


       Route::middleware([EnsurePhoneNumberIsVerified::class])->group(function () {
           Route::get('/checkout/address', [CheckoutController::class, 'selectAddress'])->name('checkout.address');
           Route::post('/checkout/address', [CheckoutController::class, 'confirmAddress'])->name('checkout.confirm-address');
           Route::get('/checkout/payment', [CheckoutController::class, 'showPayment'])->name('checkout.payment.choose-method');
           Route::post('/checkout/payment', [CheckoutController::class, 'processPayment'])->name('checkout.payment.process');
       });


        Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    });

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('auth.login-page'); // or wherever you want to send them
    })->name('logout');

});


