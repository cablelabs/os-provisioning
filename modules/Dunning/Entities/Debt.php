<?php

namespace Modules\Dunning\Entities;

use Modules\ProvBase\Entities\Contract;
use Modules\BillingBase\Providers\Currency;

class Debt extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'debt';

    public $addedBySpecialMatch;

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
}

class DebtObserver
{
    public function updated($debt)
    {
    }
}
