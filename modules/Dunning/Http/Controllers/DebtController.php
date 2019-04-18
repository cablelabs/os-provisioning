<?php

namespace Modules\Dunning\Http\Controllers;

// use Modules\Dunning\Entities\Debt;

class DebtController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        return [
            ['form_type' => 'text', 'name' => 'contract_id', 'description' => 'Contract', 'hidden' => 1],
            ['form_type' => 'text', 'name' => 'amount', 'description' => 'Amount'],
            ['form_type' => 'text', 'name' => 'fee', 'description' => 'Fee'],
            ['form_type' => 'text', 'name' => 'date', 'description' => 'Date'],         // Belegdatum
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];
    }
}
