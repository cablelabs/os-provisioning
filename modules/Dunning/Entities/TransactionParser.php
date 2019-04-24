<?php

namespace Modules\Dunning\Entities;

/**
 * This class is intended to parse a Kingsquare\Banking\Transaction and create a \Modules\Dunning\Entities\Dept of it
 * This implements the parsing of the transactions description field that can vary from bank to bank, what is missing in the Kingsquare composer package
 * The class is structured as \Kingsquare\Parser\Banking\Mt940
 *
 * @author Nino Ryschawy
 */
class TransactionParser
{
    public $engine;

    /**
     * Parse the given transaction and create an \Modules\Dunning\Entities\Dept object.
     *
     * @return object
     */
    public function parse(\Kingsquare\Banking\Transaction $transaction, TransactionParserEngine $engine = null)
    {
        if (! $transaction || $transaction->getPrice() == 0) {
            return;
        }

        // Get engine only once - assume here that parsing one MT940 file only needs one engine
        if ($engine) {
            $this->engine = $engine;
        } elseif (! $this->engine) {
            $this->engine = TransactionParserEngine::__getInstance($transaction);
        }

        if (! $this->engine instanceof TransactionParserEngine) {
            \Log::error('Engine does not extend \Modules\Dunning\Entities\TransactionParserEngine');

            return;
        }

        $debt = $this->engine->parse($transaction);

        // TODO: Check if debt already exists

        return $debt;
    }
}
