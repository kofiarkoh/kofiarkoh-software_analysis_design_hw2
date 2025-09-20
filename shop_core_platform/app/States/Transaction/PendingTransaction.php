<?php

namespace App\States\Transaction;

use App\States\TransactionState;

class PendingTransaction extends TransactionState
{
    public static $name = 'pending';

    public function label(): string
    {
        return 'Pending';
    }
}
