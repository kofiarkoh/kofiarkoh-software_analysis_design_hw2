<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OtpToken extends Model
{

    protected $guarded = false;

    public function otpTokenable(): MorphTo
    {
        return $this->morphTo();
    }
}
