<?php

namespace Modules\HfcReq\Http\Controllers;

use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;
use Modules\HfcBase\Http\Controllers\HfcBaseController;

class NetElementController extends HfcBaseController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		if ($model) {
			$parents = $model->parents_list();
		}
		else
		{
			$model = new NetElement;
			$parents = $model->first()->parents_list_all();
		}

		$kml_files = $model->kml_files();

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			// array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => ['NET' => 'NET', 'CMTS' => 'CMTS', 'DATA' => 'DATA', 'CLUSTER' => 'CLUSTER', 'NODE' => 'NODE', 'AMP' => 'AMP']),
			array('form_type' => 'select', 'name' => 'netelementtype_id', 'description' => 'NetElement Type', 'value' => $model->html_list(NetElementType::all(), 'name')),
			array('form_type' => 'text', 'name' => 'ip', 'description' => 'IP address'),
			array('form_type' => 'text', 'name' => 'link', 'description' => 'HTML Link'),
			array('form_type' => 'text', 'name' => 'pos', 'description' => 'Geoposition'),
			array('form_type' => 'select', 'name' => 'parent', 'description' => 'Parent Object', 'value' => $parents),
			array('form_type' => 'select', 'name' => 'state', 'description' => 'State', 'value' => ['OK' => 'OK', 'YELLOW' => 'YELLOW', 'RED' => 'RED'], 'options' => ['readonly']),
			array('form_type' => 'text', 'name' => 'options', 'description' => 'Options'),

			array('form_type' => 'select', 'name' => 'kml_file', 'description' => 'Choose KML file', 'value' => $kml_files),
			array('form_type' => 'file', 'name' => 'kml_file_upload', 'description' => 'or: Upload KML file', 'space' => 1),

			array('form_type' => 'text', 'name' => 'community_ro', 'description' => 'Community RO'),
			array('form_type' => 'text', 'name' => 'community_rw', 'description' => 'Community RW'),
			array('form_type' => 'text', 'name' => 'address1', 'description' => 'Address Line 1'),
			array('form_type' => 'text', 'name' => 'address2', 'description' => 'Address Line 2'),
			array('form_type' => 'text', 'name' => 'address3', 'description' => 'Address Line 3'),
			array('form_type' => 'textarea', 'name' => 'descr', 'description' => 'Description'),
		);

	}


	/**
	 * Overwrites the base method
	 */
	public function store($redirect = true)
	{
		// check and handle uploaded KML files
		$this->handle_file_upload('kml_file', static::get_model_obj()->kml_path);

		// call base method
		$ret = parent::store();

		NetElement::relation_index_build_all();

		return $ret;
	}

	/**
	 * Overwrites the base method
	 */
	public function update($id)
	{
		// check and handle uploaded KML files
		$this->handle_file_upload('kml_file', static::get_model_obj()->kml_path);

		// call base method
		$ret = parent::update($id);

		NetElement::relation_index_build_all();

		return $ret;
	}

	/**
	 * Overwrites the base method
	 */
	public function destroy ($id)
	{
		// call base method
		$ret = parent::destroy($id);

		NetElement::relation_index_build_all();

		return $ret;
	}

	/**
	 * Overwrites the base method
	 * Usage: ERD - right click - delete
	 * Note: needs special GET route in routes.php
	 */
    public function delete ($id)
    {
    	parent::destroy($id);

    	NetElement::relation_index_build_all();

    	return \Redirect::back();
    }



	/**
	 * Controlling Read Function
	 *
	 * TODO: split SNMP Stuff from netelem specific stuff
	 *       and do not return a View -> instead call BaseController@edit
	 *
	 * @param id the NetElement id
	 * @author Torsten Schmidt
	 */
	public function controlling_edit($id)
	{
		// Init NetElement Model
		$netelem = NetElement::findOrFail($id);

		// Init SnmpController
		$snmp = new SnmpController;
		$snmp->init ($netelem);

		// Get Html Form Fields for generic View
		$form_fields = $snmp->snmp_get_all();

		// Init View
		$obj = static::get_model_obj();
		$model_name  = \NamespaceController::get_model_name();
		$view_header = 'Edit: '.$netelem->name;
		$view_var 	 = $obj->findOrFail($id);
		$route_name  = \NamespaceController::get_route_name();
		$view_header_links = \BaseViewController::view_main_menus();

		$view_path = 'hfcsnmp::NetElement.controlling';
		$form_path = 'Generic.form';
		$form_update = 'NetElement.controlling_update';

		//dd(compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'form_update'));


		return View::make($view_path, compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'form_update', 'route_name', 'view_header_links'));
	}


	/**
	 * Controlling Update Function
	 *
	 * @param id the NetElement id
	 * @author Torsten Schmidt
	 */
	public function controlling_update($id)
	{
		$netelem = NetElement::findOrFail($id);

		// TODO: validation
		$validator = \Validator::make($data = $this->prepare_input(\Input::all()), $netelem::rules($id));

/*
		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
*/

		// Init SnmpController
		$snmp = new SnmpController;
		$snmp->init ($netelem);

		// Set Html Form Fields for generic View

		$snmp->snmp_set_all($data);


		return \Redirect::route('NetElement.controlling_update', $id)->with('message', 'Updated!');
	}

}
