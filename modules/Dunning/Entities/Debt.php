<?php

namespace Modules\Dunning\Entities;

use Modules\ProvBase\Entities\Contract;
use Modules\BillingBase\Providers\Currency;

class Debt extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'debt';

    public $addedBySpecialMatch;

    public $debtObserverEnabled = true;

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'amount' => 'required',
            'date' => 'required|date',
            'voucher_nr' => 'required',
            'due_date' => 'date|nullable',
        ];
    }

    /**
     * Observers
     */
    public static function boot()
    {
        self::observe(new DebtObserver);
        parent::boot();
    }

    /**
     * View related stuff
     */

    // Name of View
    public static function view_headline()
    {
        return 'Debt';
    }

    public static function view_icon()
    {
        return '<i class="fa fa-usd"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->getBsClass();

        return ['table' => $this->table,
                'index_header' => ['contract.firstname', 'contract.lastname', 'debt.date', 'sum', 'amount', 'debt.total_fee' /*,'SEPA'*/],
                'header' => $this->label(),
                'bsclass' => $bsclass,
                // 'eager_loading' => ['contract.sepamandates.costcenter'],
                'eager_loading' => ['contract'],
                'edit' => [
                    'contract.firstname' => 'getContractFirstname',
                    'contract.lastname' => 'getContractLastname',
                    'sum' => 'sum',
                    // 'SEPA' => 'hasSepa',
                ],
            ];
    }

    public function getBsClass()
    {
        $bsclass = 'success';

        if ($this->sum() > 0) {
            $bsclass = 'warning';
        }

        if ($this->cleared) {
            $bsclass = $this->missing_amount >= 0 ? 'active' : 'success';
        }

        return $bsclass;
    }

    public function label()
    {
        $label = (string) ($this->sum()).Currency::get()." ($this->date)";
        $label .= ' - '.trans('dunning::view.open').': '.$this->missing_amount.Currency::get();

        return $label;
    }

    public function getContractFirstname()
    {
        return $this->contract->firstname;
    }

    public function getContractLastname()
    {
        return $this->contract->lastname;
    }

    public function sum()
    {
        return round($this->amount + $this->total_fee, 4);
    }

    public function hasSepa()
    {
    }

    public function view_belongs_to()
    {
        return $this->contract;
    }

    /**
     * Relationships:
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    // One debt can have multiple payments (debt children) that will clear the debt
    public function children()
    {
        return $this->hasMany('Modules\Dunning\Entities\Debt', 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo('Modules\Dunning\Entities\Debt');
    }

    /**
     * Return children of a debt or via invoice_id related debts
     *
     * @return array
     */
    public function getPayments()
    {
        $payments = [];

        // TODO: Config checking actually not necessary as below query would also return [] for csv type as invoice_id (inheritly) is never set
        if (config('dunning.debtMgmtType') == 'csv' || ! $this->children->isEmpty()) {
            $payments = $this->children;
        }

        // Needed for manual bank transactions from customer
        if ($this->invoice_id) {
            $comparator = $this->amount > 0 ? '<' : '>';

            $payments = Debt::where('invoice_id', $this->invoice_id)->where('amount', $comparator, 0)->where('id', '!=', $this->id)->get();
        }

        return $payments ?: [];
    }
}

class DebtObserver
{
    public function creating($debt)
    {
        if (! $debt->missing_amount) {
            $debt->missing_amount = $debt->amount;
        }

        if ($debt->parent_id === 0) {
            $existingDebt = Debt::where('contract_id', $debt->contract_id)
                ->where('amount', (-1) * $debt->amount)->where('cleared', 0)
                ->first();

            $debt->parent_id = $existingDebt ? $existingDebt->id : null;
        }
    }

    public function created($debt)
    {
        $this->clearCorrespondingDebt($debt);
    }

    public function updated($debt)
    {
        if ($debt->debtObserverEnabled) {
            if ($debt->isDirty('parent_id') && ! $debt->parent_id) {
                $this->clearCorrespondingDebt($debt, false, true);

                $debt->missing_amount = $debt->amount;
                $debt->debtObserverEnabled = false;
                $debt->save();
            } else {
                $this->clearCorrespondingDebt($debt);
            }
        }
    }

    public function deleted($debt)
    {
        if ($debt->debtObserverEnabled) {
            $this->clearCorrespondingDebt($debt, true);
        }

        Debt::where('id', $debt->id)->update(['missing_amount' => $debt->amount, 'cleared' => 0]);
    }

    /**
     * Determine debt to clear and adapt missing_amount and cleared flag of it and it's payments depending on the cumulated amounts
     */
    public function clearCorrespondingDebt($debt, $deleted = false, $original = false)
    {
        if (! $debt->invoice_id && ! $debt->parent_id && ! $original) {
            return;
        }

        $debtToClear = $this->getDebtToClear($debt, $original);

        if (! $debtToClear) {
            return;
        }

        if (($debt->amount > 0 && $debtToClear->amount > 0) || ($debt->amount < 0 && $debtToClear->amount < 0)) {
            // Clear parent id as this relation has no sense - no guilog entry needed
            $this->clearParentId($debt);

            return;
        }

        $payments = $debtToClear->getPayments();

        $sumPayed = 0;
        foreach ($payments as $payment) {
            $sumPayed += $payment->amount;
        }

        // https://stackoverflow.com/questions/17210787/php-float-calculation-error-when-subtracting
        $debtToClear->missing_amount = round($debtToClear->sum() + $sumPayed, 4);

        if (($debtToClear->amount > 0 && $debtToClear->missing_amount < 0) || ($debtToClear->amount < 0 && $debtToClear->missing_amount > 0)) {
            $debtToClear->missing_amount = 0;
        } elseif ($debtToClear->missing_amount != 0) {
            $debtToClear->cleared = 0;
        } elseif ($debtToClear->missing_amount == 0) {
            $debtToClear->cleared = 1;
        }

        // Update cleared flag of all payments belonging to a debt
        $open = $debtToClear->amount;
        foreach ($payments as $payment) {
            $payment->cleared = $debtToClear->cleared;

            if ($debtToClear->cleared) {
                $payment->missing_amount = 0;
            } else {
                if (abs($open) > abs($payment->amount)) {
                    $open += $payment->amount;
                    $payment->missing_amount = 0;
                } else {
                    // 75 - 17.34
                    $payment->missing_amount = $payment->amount + $open;
                    $open = 0;
                }
            }

            $payment->debtObserverEnabled = false;
            $payment->save();
        }

        // Show warning when clearing transaction amount is bigger than the debt - deprecated
        // if ($debtToClear->missing_amount < 0) {
        //     \Session::put('alert.warning', trans('dunning::messages.amountExceeded'));
        // }

        $debtToClear->debtObserverEnabled = false;
        $debtToClear->save();
    }

    private function clearParentId($debt)
    {
        Debt::where('id', $debt->id)->update(['parent_id' => null]);

        \Session::push('tmp_error_above_form', trans('dunning::messages.clearParentId'));
    }

    private function getDebtToClear($debt, $original = false)
    {
        // In case parent_id was removed
        if ($original) {
            return Debt::find($debt->getOriginal()['parent_id']);
        }

        if ($debt->parent_id || config('dunning.debtMgmtType') == 'csv') {
            return $debt->parent;
        }

        // Manual bank transfer from customer with invoice number
        if ($debt->invoice_id) {
            $comparator = $debt->amount > 0 ? '<' : '>';

            return Debt::where('invoice_id', $debt->invoice_id)
                ->where('cleared', 0)->where('amount', $comparator, 0)->where('id', '!=', $debt->id)
                ->orderBy('id')->first();
        }
    }
}
