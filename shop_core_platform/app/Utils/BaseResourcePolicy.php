<?php

namespace App\Utils;

use App\Models\Vendor\Vendor;

class BaseResourcePolicy
{

    public static function canAccessResource() : bool
    {
        $vendor = auth('vendor')->user();
        if ($vendor){
            return  !in_array($vendor->status, [
                Vendor::STATUS_SUSPENDED,
            ]);
        }
        return true;

    }
}
