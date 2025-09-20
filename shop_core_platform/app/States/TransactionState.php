<?php

namespace App\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class TransactionState extends State
{
    abstract public function label(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Transaction\PendingTransaction::class)
            ->allowTransition(Transaction\PendingTransaction::class, Transaction\SuccessTransaction::class)
            ->allowTransition(Transaction\PendingTransaction::class, Transaction\FailedTransaction::class)
            ->registerState([Transaction\PendingTransaction::class, Transaction\SuccessTransaction::class, Transaction\FailedTransaction::class]);
    }
}
