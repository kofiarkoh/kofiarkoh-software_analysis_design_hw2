<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRecipient extends Model
{
     protected $fillable = [
        'recipient_code', 'type', 'name', 'account_number', 'bank_code', 'currency', 'active'
    ];
}
