<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'transfer_code', 'amount', 'currency', 'reference', 'recipient', 'reason', 'source', 'status'
    ];
}
