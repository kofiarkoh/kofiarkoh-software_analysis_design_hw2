<?php

namespace App\Traits;

use App\Models\OtpToken;
use App\Utils\Arkesel;

trait VerifiesPhoneNumber
{
    /**
     * Check if phone number is verified.
     */
    public function hasVerifiedPhoneNumber(): bool
    {
        return $this->phone_number_verified_at !== null;
    }

    /**
     * Mark the phone number as verified.
     */
    public function markPhoneNumberAsVerified(): bool
    {
        $this->phone_number_verified_at = now();
        return $this->save();
    }

    /**
     * Send OTP token and notification (SMS/email/etc.).
     */
    public function sendPhoneNumberVerificationNotification(): void
    {
        $token = random_int(100000, 999999);

        $this->otpTokens()->create([
            'token' => $token,
            'expires_at' => now()->addMinutes(10),
        ]);

        $arkesel = new Arkesel();

        $message = "Your phone number verification token is {$token}";
        $arkesel->sendSMS($message, [$this->phone_number]);

    }

    /**
     * Get the phone number to be verified.
     */
    public function getPhoneNumberForVerification(): string
    {
        return $this->phone_number;
    }

    /**
     * Get latest unexpired OTP token.
     */
    public function latestUnexpiredOtpToken(): ?OtpToken
    {
        return $this->otpTokens()
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Define morphMany relationship with OtpTokens.
     */
    public function otpTokens()
    {
        return $this->morphMany(OtpToken::class, 'otpable');
    }
}
