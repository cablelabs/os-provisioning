<?php

namespace Modules\OverdueDebts\Http\Controllers;

class OverdueDebtsController extends \BaseController
{
    public function view_form_fields($model = null)
    {
        return [
            ['form_type' => 'text', 'name' => 'fee', 'description' => 'Fee for return debit notes', 'help' => trans('overduedebts::help.fee')],
            ['form_type' => 'checkbox', 'name' => 'total', 'description' => 'Total', 'help' => trans('overduedebts::help.total')],
        ];
    }
}
