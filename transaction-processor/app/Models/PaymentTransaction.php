<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'reference', 'amount', 'currency', 'email', 'status', 'access_code', 'authorization_url'
    ];
}
