<?php

namespace Modules\Dunning\Entities;

class Debt extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'debt';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'amount'          => 'required',
            // 'fee'          => 'required',
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
        return '<i class="fa fa-creative-commons"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = 'success';

        if ($this->amount > 0) {
            $bsclass = 'warning';
        }

        return ['table' => $this->table,
                'index_header' => ['contract.firstname', 'contract.lastname', 'debt.date', 'amount', 'fee', /*'SEPA'*/],
                'header' => (string) ($this->amount + $this->fee).\Modules\BillingBase\Providers\Currency::get()." ($this->date)",
                'bsclass' => $bsclass,
                'eager_loading' => ['contract.sepamandates.costcenter'],
                'edit' => [
                    'contract.firstname' => 'getContractFirstname',
                    'contract.lastname' => 'getContractLastname',
                    // 'SEPA' => 'hasSepa',
                ],
            ];
    }

    public function getContractFirstname()
    {
        return $this->contract->firstname;
    }

    public function getContractLastname()
    {
        return $this->contract->lastname;
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
        return $this->belongsTo('Modules\ProvBase\Entities\Contract');
    }
}

class DebtObserver
{
    public function updated($debt)
    {
    }
}
