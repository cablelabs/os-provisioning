<?php 

namespace Modules\ProvBase\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

use App\Http\Controllers\BaseModuleController;

class ProvBaseController extends BaseModuleController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'prov_server_ip', 'description' => 'Provisioning Server IP'),
			// array('form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly']),
			// array('form_type' => 'select', 'name' => 'contract_id', 'description' => 'Contract', 'value' => $model->html_list($model->contracts(), 'id')),
			// array('form_type' => 'checkbox', 'name' => 'public', 'description' => 'Public CPE', 'value' => '1'),
			);
	}
	
}