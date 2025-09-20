<?php

namespace App\States\Transaction;

use App\States\TransactionState;

class FailedTransaction extends TransactionState
{
    public static $name = 'failed';

    public function label(): string
    {
        return 'Failed';
    }
}
