<?php

namespace Modules\Dunning\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class DunningController extends \BaseController
{
    public function view_form_fields($model = null)
    {
        return [
            ['form_type' => 'text', 'name' => 'fee', 'description' => 'Fee for return debit notes', 'help' => trans('dunning::help.fee')],
            ['form_type' => 'checkbox', 'name' => 'total', 'description' => 'Total', 'help' => trans('dunning::help.total')],
        ];
    }
}
