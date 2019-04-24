<?php

namespace Modules\Dunning\Entities;

abstract class TransactionParserEngine
{
    /**
     * Reads the Description of the Transaction to guess which engine to use for parsing.
     *
     * @param string $string
     *
     * @return Engine
     */
    public static function __getInstance(\Kingsquare\Banking\Transaction $transaction)
    {
        // TODO: Detect Bank and return appropriate parser when there are more than this one

        return new SpkTransactionParser;
    }

    abstract public function parse(\Kingsquare\Banking\Transaction $transaction);
}
