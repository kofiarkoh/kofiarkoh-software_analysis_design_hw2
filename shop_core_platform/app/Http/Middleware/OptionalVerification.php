<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;

class OptionalVerification
{
    public function handle($request, Closure $next)
    {

        // Guests can pass freely
        if (! Auth::check()) {
            return $next($request);
        }

        // Authenticated users must pass these middlewares
        return app(Pipeline::class)
            ->send($request)
            ->through([
                \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // same as 'verified'
                \App\Http\Middleware\EnsurePhoneNumberIsVerified::class,
            ])
            ->then(function ($request) use ($next) {
                return $next($request);
            });

    }
}
