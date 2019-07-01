<?php

namespace Modules\Dunning\Entities;

use ChannelLog;

class SpkTransactionParser extends TransactionParserEngine
{
    public $excludeRegexesRelPath = 'app/config/dunning/transferExcludes.php';
    public $excludeRegexes;

    public $conf;

    public function parse(\Kingsquare\Banking\Transaction $transaction)
    {
        if ($transaction->getDebitCredit() == 'D') {
            $debt = $this->parseDebit($transaction);
            $this->addFee($debt);
        } else {
            $debt = $this->parseCredit($transaction);
        }

        if ($debt) {
            $debt->date = $transaction->getValueTimestamp('Y-m-d H:i:s');
        }

        return $debt;
    }

    private function parseDebit($transaction)
    {
        $debt = new Debt;
        $description = [];
        $holder = $iban = $invoiceNr = $mref = '';
        $debt->amount = $debt->bank_fee = 0;
        $descriptionArray = explode('?', $transaction->getDescription());

        foreach ($descriptionArray as $key => $line) {
            if (\Str::startsWith($line, '20EREF+')) {
                $invoiceNr = utf8_encode(str_replace(['20EREF', '+RG '], '', $line));
                continue;
            }

            if (\Str::startsWith($line, '21MREF+')) {
                $mref = utf8_encode(str_replace('21MREF+', '', $line));
                continue;
            }

            if (\Str::startsWith($line, '23COAM+')) {
                $debt->bank_fee = trim(str_replace('23COAM+', '', $line));
                $debt->bank_fee = str_replace(',', '.', $debt->bank_fee);
                continue;
            }

            if (\Str::startsWith($line, '24OAMT+')) {
                $debt->amount = trim(str_replace('24OAMT+', '', $line));
                $debt->amount = str_replace(',', '.', $debt->amount);
                continue;
            }

            if (\Str::startsWith($line, ['25SVWZ+', '26'])) {
                $description[] = trim(str_replace('SVWZ+', '', substr($line, 2)));
                continue;
            }

            if (\Str::startsWith($line, '31')) {
                $iban = utf8_encode(substr($line, 2));
                continue;
            }

            if (\Str::startsWith($line, '32')) {
                $holder = utf8_encode(substr($line, 2));
                continue;
            }
        }

        // Use module specific language file or better global file because of Crowdin?
        $logmsg = trans('dunning::messages.transaction.default.debit', [
            'holder' => $holder,
            'invoiceNr' => $invoiceNr,
            'mref' => $mref,
            'price' => number_format_lang($transaction->getPrice()),
            'iban' => $iban,
            ]);
        // $logmsg = "Transaction of $holder with invoice NR $invoiceNr, SepaMandate reference $mref, price ".$transaction->getPrice().", IBAN $iban";

        // Get SepaMandate by iban & mref
        $sepamandate = \Modules\BillingBase\Entities\SepaMandate::withTrashed()
            ->where('reference', $mref)->where('iban', $iban)
            ->orderBy('deleted_at')->orderBy('valid_from', 'desc')->first();

        if ($sepamandate) {
            $debt->sepamandate_id = $sepamandate->id;
        }

        // Get Invoice
        $invoice = \Modules\BillingBase\Entities\Invoice::where('number', $invoiceNr)->where('type', 'Invoice')->first();

        if ($invoice) {
            $debt->invoice_id = $invoice->id;

            // Check if Transaction refers to same Contract via SepaMandate and Invoice
            if ($sepamandate && $sepamandate->contract_id != $invoice->contract_id) {
                ChannelLog::notice('dunning', trans('view.Discard')." $logmsg. ".trans('dunning::messages.transaction.debit.diffContractSepa'));

                return;
            }

            // Assign Debt by invoice number (or invoice number and SepaMandate)
            $debt->contract_id = $invoice->contract_id;
        } else {
            if (! $sepamandate) {
                ChannelLog::info('dunning', trans('view.Discard')." $logmsg. ".trans('dunning::messages.transaction.debit.missSepaInvoice'));

                return;
            }

            // Assign Debt by sepamandate
            $debt->contract_id = $sepamandate->contract_id;
        }

        $debt->description = utf8_encode(implode('; ', $description));

        ChannelLog::debug('dunning', trans('dunning::messages.transaction.create')." $logmsg");

        return $debt;
    }

    private function parseCredit($transaction)
    {
        $debt = new Debt;
        $descriptionArray = explode('?', $transaction->getDescription());
        $reason = [];
        $holder = '';

        // Credit (Überweisung)
        foreach ($descriptionArray as $key => $line) {
            // Transfer reason is 20 + 21 + 22 + 23
            if (\Str::startsWith($line, ['20', '21', '22', '23'])) {
                $str = str_replace(['SVWZ', 'EREF'], '', substr($line, 2));
                $reason[] = trim(str_replace('+', ' ', $str));
            }

            // IBAN is usually not existent in the DB for credits - we could still try to check as it would find the customer in at least some cases
            if (\Str::startsWith($line, '31')) {
                $iban = utf8_encode(substr($line, 2));
                continue;
            }

            if (\Str::startsWith($line, '32')) {
                $holder = substr($line, 2);
                continue;
            }

            if (\Str::startsWith($line, '33')) {
                $holder .= substr($line, 2);
                continue;
            }
        }

        // Concatenate standard part of log message
        $holder = utf8_encode($holder);
        $price = number_format_lang($transaction->getPrice());
        $reason = utf8_encode(implode('', $reason));
        $logmsg = trans('dunning::messages.transaction.default.credit', ['holder' => $holder, 'price' => $price, 'iban' => $iban, 'reason' => $reason]);
        // $logmsg = "Transaction of $holder with price $price, IBAN $iban and transfer reason '$reason'";

        $ret = $this->searchNumbers($reason);

        if ($ret['exclude']) {
            ChannelLog::info('dunning', trans('view.Discard')." $logmsg. ".trans('dunning::messages.transaction.credit.missInvoice'));

            return;
        }

        // if (! $ret['contractNr'] && ! $ret['invoiceNr']) {
        //     ChannelLog::debug('dunning', "$logmsg discarded. ");

        //     return;
        // }

        $contract = $invoice = $sepamandate = null;
        if ($ret['contractNr']) {
            $contract = \Modules\ProvBase\Entities\Contract::where('number', $ret['contractNr'])->first();
            $ident[] = 'contract nr '.$ret['contractNr'];
        }
        if ($ret['invoiceNr']) {
            $invoice = \Modules\BillingBase\Entities\Invoice::where('number', $ret['invoiceNr'])->where('type', 'Invoice')->first();
            $ident[] = 'invoice nr '.$ret['invoiceNr'];
        }

        $sepamandate = \Modules\BillingBase\Entities\SepaMandate::where('iban', $iban)
            ->orderBy('valid_from', 'desc')
            ->where('valid_from', '<=', $transaction->getValueTimestamp('Y-m-d'))
            ->where(whereLaterOrEqual('valid_to', $transaction->getValueTimestamp('Y-m-d')))
            ->first();

        if (! ($contract || $invoice || $sepamandate)) {
            ChannelLog::notice('dunning', trans('view.Discard')." $logmsg. ".trans('dunning::messages.transaction.credit.missAll'));

            return;
        }

        // Determine contract id and log mismatches
        if ($contract) {
            if ($invoice) {
                if ($contract->id != $invoice->contract_id) {
                    ChannelLog::info('dunning', trans('view.Discard')." $logmsg. ".trans('dunning::messages.transaction.credit.diff.contractInvoice', [
                        'contract' => $contract->number,
                        'invoice' => $invoice->contract->number,
                        ]));

                    return;
                }
            }

            if ($sepamandate) {
                if ($contract->id != $sepamandate->contract_id) {
                    ChannelLog::notice('dunning', trans('view.Discard')." $logmsg. ".trans('dunning::messages.transaction.credit.diff.contractSepa', [
                        'contract' => $contract->number,
                        'sepamandate' => $sepamandate->contract->number,
                        ]));

                    return;
                }

                $debt->sepamandate_id = $sepamandate->id;
            }

            $debt->contract_id = $contract->id;
        } elseif ($invoice) {
            if ($sepamandate) {
                if ($sepamandate->contract_id != $invoice->contract_id) {
                    ChannelLog::notice('dunning', trans('view.Discard')." $logmsg. ".trans('dunning::messages.transaction.credit.diff.invoiceSepa', [
                        'sepamandate' => $sepamandate->contract->number,
                        'invoice' => $invoice->contract->number,
                        ]));

                    return;
                }

                $debt->sepamandate_id = $sepamandate->id;
            }

            $debt->contract_id = $invoice->contract_id;
        } else {
            $debt->contract_id = $sepamandate->contract_id;
        }

        $debt->amount = -1 * $transaction->getPrice();
        $debt->description = $reason;

        ChannelLog::debug('dunning', trans('dunning::messages.transaction.create')." $logmsg");

        return $debt;
    }

    private function addFee($debt)
    {
        // lazy loading of global dunning conf
        if (is_null($this->conf)) {
            $this->loadConf();
        }

        if ($debt && $this->conf['fee']) {
            $debt->total_fee = $this->conf['total'] ? $this->conf['fee'] : $debt->bank_fee + $this->conf['fee'];
        }

        return $debt;
    }

    /**
     * Load dunning model and store relevant config in global variable
     */
    private function loadConf()
    {
        $conf = Dunning::first();

        $this->conf = [
            'fee' => $conf->fee,
            'total' => $conf->total,
        ];
    }

    /**
     * Search for contract and invoice number in credit transfer reason to assign debt to appropriate customer
     *
     * @param string
     * @return array
     */
    private function searchNumbers($transferReason)
    {
        // Match examples: Rechnungsnummer|Rechnungsnr|RE.-NR.|RG.-NR.|RG 2018/3/48616
        preg_match('/R(.*?)((n(.*?)r)|G)(.*?)(\d{4}\/\d+\/\d+)/i', $transferReason, $matchInvoice);
        $invoiceNr = $matchInvoice ? $matchInvoice[6] : '';

        // Match examples: Kundennummer|Kd-Nr|Kd.nr.|Kd.-Nr. 13451
        preg_match('/K(.*?)d(.*?)n(.*?)r(.*?)([1-7]\d{1,4})/i', $transferReason, $matchContract);
        $contractNr = $matchContract ? $matchContract[5] : 0;

        // Special invoice numbers that ensure that transaction definitely doesn't belong to NMSPrime
        if (is_null($this->excludeRegexes)) {
            $this->loadExcludeRegexes();
        }

        $exclude = '';
        foreach ($this->excludeRegexes as $regex => $group) {
            preg_match($regex, $transferReason, $matchInvoiceSpecial);

            if ($matchInvoiceSpecial) {
                $exclude = $matchInvoiceSpecial[$group];

                break;
            }
        }

        return [
            'contractNr' => $contractNr,
            'invoiceNr' => $invoiceNr,
            'exclude' => $exclude,
        ];
    }

    private function loadExcludeRegexes()
    {
        if (! \Storage::exists('config/dunning/transferExcludes')) {
            $this->excludeRegexes = include storage_path($this->excludeRegexesRelPath);
        }

        if (! $this->excludeRegexes) {
            $this->excludeRegexes = [];
        }
    }
}
