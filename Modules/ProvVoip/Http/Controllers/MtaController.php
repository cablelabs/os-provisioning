<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\Mta;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Configfile;


class MtaController extends \BaseModuleController {


	protected $index_create_allowed = false;

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'mac', 'description' => 'MAC address'),
			array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly']),
			array('form_type' => 'select', 'name' => 'modem_id', 'description' => 'Modem', 'value' => $model->html_list($model->modems(), 'hostname')),
			array('form_type' => 'select', 'name' => 'configfile_id', 'description' => 'Configfile', 'value' => $model->html_list($model->configfiles(), 'name')),
			array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => Mta::getPossibleEnumValues('type', true)),
		);
	}


}
