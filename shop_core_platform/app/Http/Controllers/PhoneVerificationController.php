<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class PhoneVerificationController extends Controller implements HasMiddleware
{


    public static function middleware(): array
    {

        return [
            function (Request $request, \Closure $next) {
                /** @var User $user */
                $user = Auth::user();
                if ($user->hasVerifiedPhoneNumber()) {
                    return redirect()->intended('/')->with('success', 'Phone number already verified verified!');
                }

                return $next($request);
            }
        ];

    }


    public function showNotice()
    {

        return view('auth.verify-phone');
    }

    public function showVerificationForm()
    {
        return view('auth.verify-phone-otp-form');
    }

    public function sendToken(Request $request){


        /** @var User $user */
        $user = Auth::user();

       $user->sendPhoneNumberVerificationNotification();

        return redirect()->route('verify.phone.verification-form')->with('success', 'Verification code sent!');
    }
    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $token = $user->otpTokens()
            ->where('token', $request->token)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$token) {
            return back()->withErrors(['token' => 'Invalid or expired token']);
        }

        $user->markPhoneNumberAsVerified();
        return redirect()->intended('/')->with('success', 'Phone number verified!');
    }
}
