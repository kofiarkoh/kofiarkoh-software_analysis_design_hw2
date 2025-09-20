<?php

namespace App\States\Transaction;

use App\States\TransactionState;

class SuccessTransaction extends TransactionState
{
    public static $name = 'success';

    public function label(): string
    {
        return 'Success';
    }
}
