<?php

namespace Modules\Dunning\Entities;

/**
 * This class is intended to parse a Kingsquare\Banking\Transaction and create a \Modules\Dunning\Entities\Debt of it
 *
 * Tasks:
 *  Detect correct parser (engine)
 *  Call parse function of parser
 *  Check if debt exists
 *
 * @author Nino Ryschawy
 */
class TransactionParser
{
    public $engine;
    private $transaction;

    public function __construct(\Kingsquare\Banking\Transaction $transaction, $text)
    {
        $this->transaction = $transaction;
        $this->engine = $this->detectParser($text);
    }

    /**
     * Determines the correct parser dependent of the Mt940.sta file
     *
     * @param string
     * @return DefaultTransactionParser
     */
    private function detectParser($text)
    {
        // Actually works for Sparkasse and Volksbank
        return new DefaultTransactionParser($this->transaction);
        // return new SpkTransactionParser;
    }

    /**
     * Parse the given transaction and create an \Modules\Dunning\Entities\Dept object.
     *
     * @return object
     */
    public function parse()
    {
        if (! $this->transaction || $this->transaction->getPrice() == 0) {
            return;
        }

        $debt = $this->engine->parse();

        if ($this->debtExists($debt)) {
            return;
        }

        return $debt;
    }

    /**
     * Checks if debt was already added by same or another uploaded transaction.sta file
     */
    public function debtExists($debt)
    {
        if (! $debt) {
            return false;
        }

        $exists = Debt::where('date', $debt->date)->where('description', $debt->description)->where('amount', $debt->amount)
            ->where('bank_fee', $debt->bank_fee ?: 0)->where('contract_id', $debt->contract_id)
            ->count();

        if ($exists) {
            $debitCredit = $this->transaction->getDebitCredit() == 'C' ? 'Credit' : 'Debit';
            \ChannelLog::debug('dunning', trans('dunning::messages.transaction.exists', [
                'debitCredit' => trans("view.$debitCredit"),
                'description' => $this->transaction->getDescription(),
                'price' => $this->transaction->getPrice(),
                ]));

            return true;
        }

        return false;
    }
}
