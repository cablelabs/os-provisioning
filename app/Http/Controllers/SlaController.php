<?php

namespace App\Http\Controllers;

class SlaController extends BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        return [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'options' => ['placeholder' => 'xs | '.implode(' | ', \App\Sla::$names)]],
            ['form_type' => 'text', 'name' => 'license', 'description' => 'License'],
            // ['form_type' => 'text', 'name' => 'num_contracts', 'description' => ''],
            // ['form_type' => 'text', 'name' => 'num_modems', 'description' => ''],
            // ['form_type' => 'text', 'name' => 'num_cmts', 'description' => ''],
            // ['form_type' => 'text', 'name' => 'system_status', 'description' => ''],
            ];
    }

    /**
     * Set Session key that is used later when support request is actually made
     */
    public function clicked_sla()
    {
        \Session::push('clicked_sla', true);
        \Log::debug('Get SLA clicked');
    }
}
