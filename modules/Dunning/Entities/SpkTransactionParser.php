<?php

namespace Modules\Dunning\Entities;

class SpkTransactionParser extends TransactionParserEngine
{
    public function parse(\Kingsquare\Banking\Transaction $transaction)
    {
        if ($transaction->getDebitCredit() == 'D') {
            $debt = self::parseDebit($transaction);
        } else {
            $debt = self::parseCredit($transaction);
        }

        if ($debt) {
            // $debt->date = \Carbon\Carbon::createFromTimestamp($transaction->getValueTimestamp());
            $debt->date = $transaction->getValueTimestamp('Y-m-d H:i:s');
        }

        return $debt;
    }

    private static function parseDebit($transaction)
    {
        $debt = new Debt;
        $description = [];
        $holder = $iban = $invoiceNr = $mref = '';
        $debt->amount = $debt->fee = 0;
        $descriptionArray = explode('?', $transaction->getDescription());

        foreach ($descriptionArray as $key => $line) {
            if (\Str::startsWith($line, '20EREF+')) {
                $invoiceNr = str_replace(['20EREF', '+RG '], '', $line);
                continue;
            }

            if (\Str::startsWith($line, '21MREF+')) {
                $mref = str_replace('21MREF+', '', $line);
                continue;
            }

            if (\Str::startsWith($line, '23COAM+')) {
                $debt->fee = trim(str_replace('23COAM+', '', $line));
                $debt->fee = str_replace(',', '.', $debt->fee);
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
                $iban = substr($line, 2);
                continue;
            }

            if (\Str::startsWith($line, '32')) {
                $holder = substr($line, 2);
                continue;
            }
        }

        // Use module specific language file or better global file because of Crowdin?
        // $logmsg = trans('Dunning::messages.transactionLog', ['invoiceNr' => $invoiceNr, 'reference' => $mref, 'price' => $price, 'iban' => $iban]);
        $logmsg = "MT940: Transaction of $holder with invoice NR $invoiceNr, SepaMandate reference $mref, price ".$transaction->getPrice().", IBAN $iban";

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
                \Log::notice("$logmsg discarded. Referenced SepaMandate and Invoice belong to different contract.");

                return;
            }

            // Assign Debt by invoice number (or invoice number and SepaMandate)
            $debt->contract_id = $invoice->contract_id;
        } else {
            if (! $sepamandate) {
                \Log::debug("$logmsg discarded. Neither SepaMandate nor invoice nr could be found in the database.");

                return;
            }

            // Assign Debt by sepamandate
            $debt->contract_id = $sepamandate->contract_id;
        }

        $debt->description = implode('; ', $description);

        \Log::debug("$logmsg will add a debt if it doesnt exist yet.");

        return $debt;
    }

    private static function parseCredit($transaction)
    {
        $debt = new Debt;
        $descriptionArray = explode('?', $transaction->getDescription());
        $reason = [];
        $holder = $iban = '';

        // Credit (Überweisung)
        foreach ($descriptionArray as $key => $line) {
            // Transfer reason is 20 + 21 + 22 + 23
            if (\Str::startsWith($line, ['20', '21', '22', '23'])) {
                $str = str_replace(['SVWZ', 'EREF'], '', substr($line, 2));
                $reason[] = str_replace('+', ' ', $str);
            }

            // IBAN is usually not existent in the DB for credits - we could still try to check as it would find the customer in at least some cases
            if (\Str::startsWith($line, '31')) {
                $iban = substr($line, 2);
                continue;
            }

            if (\Str::startsWith($line, '32')) {
                $holder = substr($line, 2);
                continue;
            }
        }

        $reason = implode('', $reason);
        $ret = self::searchNumbers($reason);

        $contract = $invoice = $sepamandate = null;
        if ($ret['contractNr']) {
            $contract = \Modules\ProvBase\Entities\Contract::where('number', $ret['contractNr'])->first();
            $ident[] = 'contract nr '.$ret['contractNr'];
        }
        if ($ret['invoiceNr']) {
            $invoice = \Modules\BillingBase\Entities\Invoice::where('number', $ret['invoiceNr'])->where('type', 'Invoice')->first();
            $ident[] = 'invoice nr '.$ret['invoiceNr'];
        }
        if ($iban) {
            $sepamandate = \Modules\BillingBase\Entities\SepaMandate::where('sepa_iban', $iban)
                ->orderBy('sepa_valid_from', 'desc')->first();
        }

        $ident[] = 'price '.$transaction->getPrice();
        $ident[] = "IBAN $iban";
        $ident = implode(',', $ident);
        $logmsg = "MT940: Transaction of $holder with $ident";

        $sepamandate = \Modules\BillingBase\Entities\SepaMandate::where('iban', $iban)
            ->orderBy('valid_from', 'desc')->first();

        if (! ($contract || $invoice || $sepamandate)) {
            \Log::notice("$logmsg discarded. Neither contract, nor invoice, nor sepa mandate could be found.");

            return;
        }

        // Determine contract id and log mismatches
        if ($contract) {
            if ($invoice && $contract->id != $invoice->contract_id) {
                \Log::debug("$logmsg discarded. Contract and invoice number from transfer reason do not match to the same contract in the database.");

                return;
            }

            if ($sepamandate && $contract->id != $sepamandate->contract_id) {
                \Log::debug("$logmsg discarded. Found sepamandate belongs to different contract ($contract->number).");

                return;
            }

            $debt->contract_id = $contract->id;
        } elseif ($invoice) {
            if ($sepamandate && $sepamandate->contract_id != $invoice->contract_id) {
                \Log::debug("$logmsg discarded. Found sepamandate belongs to different contract ($contract->number) than the found invoice (".$invoice->contract->number.').');

                return;
            }

            $debt->contract_id = $invoice->contract_id;
        } else {
            $debt->contract_id = $sepamandate->contract_id;
        }

        $debt->amount = -1 * $transaction->getPrice();

        \Log::debug("$logmsg will add a debt if it doesnt exist yet.");

        return $debt;
    }

    /**
     * Search for contract and invoice number in credit transfer reason to assign debt to appropriate customer
     *
     * @TODO: Find numbers language dependent
     *
     * @param string
     * @return array
     */
    private static function searchNumbers($transferReason)
    {
        // Match examples: Rechnungsnummer|Rechnungsnr|RE.-NR.|RG.-NR. 2018/3/48616
        preg_match('/R(.*?)n(.*?)r(.*?)(\d{4}\/\d+\/\d+)/i', $transferReason, $matchInvoice);
        $invoiceNr = $matchInvoice ? $matchInvoice[4] : 0;

        // Match examples: Kundennummer|Kd-Nr|Kd.nr.|Kd.-Nr. 13451
        preg_match('/K(.*?)d(.*?)n(.*?)r(.*?)(\d{1,5})/i', $transferReason, $matchContract);
        $contractNr = $matchContract ? $matchContract[5] : 0;

        return [
            'invoiceNr' => $invoiceNr,
            'contractNr' => $contractNr,
        ];
    }
}
