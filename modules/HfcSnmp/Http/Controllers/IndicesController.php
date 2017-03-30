<?php

namespace Modules\HfcSnmp\Http\Controllers;
use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;
use Modules\HfcSnmp\Entities\Parameter;

class IndicesController extends \BaseController {

	/**
	 * defines the formular fields for the edit and create view
	 */
	public function view_form_fields($model = null)
	{
		$netelement_id = $model->netelement_id ? : \Request::input('netelement_id');

		$netelem = NetElement::find($netelement_id);
		$params  = NetElementType::param_list($netelem->netelementtype_id);

		// label has to be the same like column in sql table
		$a = array(
			array('form_type' => 'text', 'name' => 'netelement_id', 'description' => 'NetElement', 'hidden' => 1),
			array('form_type' => 'select', 'name' => 'parameter_id', 'description' => 'Parameter', 'value' => $params),
			array('form_type' => 'text', 'name' => 'indices', 'description' => 'Indices'),
			);

		return $a;
	}

}
