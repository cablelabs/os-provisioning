<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcSnmp\Entities\Device;
use View;
use Validator;
use Input;
use Redirect;


class DeviceController extends SnmpController {

    /**
     * defines the formular fields for the edit and create view
     */
	public function get_form_fields($model = null)
	{
		if (!$model)
			$model = new Device;

		// label has to be the same like column in sql table
		return array(
			array('form_type' => 'select', 'name' => 'devicetype_id', 'description' => 'Device Type', 'value' => $model->html_list($model->devicetypes(), 'name')),
			array('form_type' => 'text', 'name' => 'name', 'description' => 'Name'),
			array('form_type' => 'text', 'name' => 'ip', 'description' => 'IP address'),
			array('form_type' => 'text', 'name' => 'community_ro', 'description' => 'Community RO'),
			array('form_type' => 'text', 'name' => 'community_rw', 'description' => 'Community RW'),
			array('form_type' => 'text', 'name' => 'address1', 'description' => 'Address Line 1'),
			array('form_type' => 'text', 'name' => 'address2', 'description' => 'Address Line 2'),
			array('form_type' => 'text', 'name' => 'address3', 'description' => 'Address Line 3'),
			array('form_type' => 'textarea', 'name' => 'description', 'description' => 'Description')
		);
	}


	/**
	 * Controlling Read Function
	 *
	 * TODO: split SNMP Stuff from device specific stuff
	 *       and do not return a View -> instead call BaseController@edit
	 *
	 * @param id the Device id
	 * @author Torsten Schmidt
	 */
	public function controlling_edit($id)
	{
		// Init Device Model
		$device = Device::findOrFail($id);

		// Init SnmpController
		$snmp = new SnmpController;
		$snmp->init ($device);

		// Get Html Form Fields for generic View
		$form_fields = $snmp->snmp_get_all();

		// Init View
		$obj = $this->get_model_obj();
		$model_name  = $this->get_model_name();
		$view_header = 'Edit: '.$device->name;
		$view_var 	 = $obj->findOrFail($id);
		$route_name  = $this->get_route_name();
		$view_header_links = BaseViewController::view_main_menus();

		$view_path = 'hfcsnmp::Device.controlling';
		$form_path = 'Generic.form';
		$form_update = 'Device.controlling_update';

		//dd(compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'form_update'));


		return View::make($view_path, compact('model_name', 'view_var', 'view_header', 'form_path', 'form_fields', 'form_update', 'route_name', 'view_header_links'));
	}


	/**
	 * Controlling Update Function
	 *
	 * @param id the Device id
	 * @author Torsten Schmidt
	 */
	public function controlling_update($id)
	{
		$device = Device::findOrFail($id);

		// TODO: validation
		$validator = Validator::make($data = $this->prepare_input(Input::all()), $device::rules($id));

/*
		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
*/

		// Init SnmpController
		$snmp = new SnmpController;
		$snmp->init ($device);

		// Set Html Form Fields for generic View

		$snmp->snmp_set_all($data);


		return Redirect::route('Device.controlling_update', $id)->with('message', 'Updated!');
	}

}
