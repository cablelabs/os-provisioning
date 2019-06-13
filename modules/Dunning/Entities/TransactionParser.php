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
            throw new Exception('Engine does not extend \Modules\Dunning\Entities\TransactionParserEngine');

            return;
        }

        $debt = $this->engine->parse($transaction);

        if ($this->debtExists($debt, $transaction)) {
            return;
        }

        return $debt;
    }

    /**
     * Checks if debt was already added by same or another uploaded transaction.sta file
     */
    public function debtExists($debt, $transaction)
    {
        if (! $debt) {
            return false;
        }

        $exists = Debt::where('date', $debt->date)->where('description', $debt->description)->where('amount', $debt->amount)
            ->where('bank_fee', $debt->bank_fee ?: 0)->where('contract_id', $debt->contract_id)
            ->count();

        if ($exists) {
            $debitCredit = $transaction->getDebitCredit() == 'C' ? 'Credit' : 'Debit';
            \ChannelLog::debug('dunning', trans('dunning::messages.transaction.exists', [
                'debitCredit' => trans("view.$debitCredit"),
                'description' => $transaction->getDescription(),
                'price' => $transaction->getPrice(),
                ]));

            return true;
        }

        return false;
    }
}
