<?php

namespace Modules\ProvVoip\Http\Controllers;

use View;
use App\Http\Controllers\BaseController;

class ProvVoipController extends BaseController
{
    public function index()
    {
        $title = 'Prov Voip Dashboard';

        return View::make('provvoip::index', $this->compact_prep_view(compact('title')));
    }

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        return [
            ['form_type' => 'text', 'name' => 'startid_mta', 'description' => 'Start ID MTA´s'],
            ['form_type' => 'text', 'name' => 'mta_domain', 'description' => 'MTA Domain', 'help' => 'Specify a Domain name here if MTA\'s need a separate Domain for Provisioning'],
            ];
    }
}
