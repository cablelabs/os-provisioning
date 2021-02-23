<?php

namespace Modules\HfcReq\Http\Controllers;

use Module;
use Request;
use App\Http\Controllers\BaseViewController;
use App\Http\Controllers\NamespaceController;

class HfcReqController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        // label has to be the same like column in sql table
        $a = [
            ['form_type' => 'text', 'name' => 'ro_community', 'description' => 'SNMP Read Only Community'],
            ['form_type' => 'text', 'name' => 'rw_community', 'description' => 'SNMP Read Write Community', 'space' => 1],
        ];

        $b = [];
        if (Module::collections()->has('Satkabel')) {
            $b = [
                ['form_type' => 'text', 'name' => 'rkm_server', 'description' => 'RKM Server '.trans('messages.Address'), 'help' => trans('hfcsnmp::help.rkmServerAddress'), 'options' => ['placeholder' => '172.20.0.10:1700']],
                ['form_type' => 'text', 'name' => 'rkm_server_username', 'description' => 'RKM Server '.trans('messages.Username')],
                ['form_type' => 'text', 'name' => 'rkm_server_password', 'description' => 'RKM Server '.trans('messages.Password'), 'space' => 1],

                ['form_type' => 'text', 'name' => 'video_controller', 'description' => 'Video Controlling Server '.trans('messages.Address'), 'options' => ['placeholder' => '172.20.0.11:1701']],
                ['form_type' => 'text', 'name' => 'video_controller_username', 'description' => 'RKM Server '.trans('messages.Username')],
                ['form_type' => 'text', 'name' => 'video_controller_password', 'description' => 'RKM Server '.trans('messages.Password'), 'space' => 1],

                ['form_type' => 'text', 'name' => 'video_encoder', 'description' => 'Video Encoding Server '.trans('messages.Address'), 'options' => ['placeholder' => '172.20.0.12:1702'], 'space' => 1],
            ];
        }

        $c = [];
        // if (Module::collections()->has('HfcCustomer')) {
        //     $c = [
        //         ['form_type' => 'text', 'name' => 'us_single_warning', 'description' => 'Upstream single warning threshhold', 'options' => ['placeholder' => '50']],
        //         ['form_type' => 'text', 'name' => 'us_single_critical', 'description' => 'Upstream single critical threshhold', 'options' => ['placeholder' => '55']],
        //         ['form_type' => 'text', 'name' => 'us_avg_warning', 'description' => 'Upstream average Warning Threshhold', 'options' => ['placeholder' => '45']],
        //         ['form_type' => 'text', 'name' => 'us_avg_critical', 'description' => 'Upstream average critical Threshhold', 'options' => ['placeholder' => '52'], 'space' => 1],
        //         ['form_type' => 'text', 'name' => 'online_absolute_minor', 'description' => 'Absolute Modem Offline Threshhold: Minor', 'options' => ['placeholder' => '5']],
        //         ['form_type' => 'text', 'name' => 'online_absolute_major', 'description' => 'Absolute Modem Offline Threshhold: Major', 'options' => ['placeholder' => '25']],
        //         ['form_type' => 'text', 'name' => 'online_absolute_critical', 'description' => 'Absolute Modem Offline Threshhold: Critical', 'options' => ['placeholder' => '100'], 'space' => 1],
        //         ['form_type' => 'text', 'name' => 'online_percentage_minor', 'description' => 'Percentage Modem Offline Threshhold: Minor', 'options' => ['placeholder' => '34']],
        //         ['form_type' => 'text', 'name' => 'online_percentage_major', 'description' => 'Percentage Modem Offline Threshhold: Major', 'options' => ['placeholder' => '51']],
        //         ['form_type' => 'text', 'name' => 'online_percentage_critical', 'description' => 'Percentage Modem Offline Threshhold: Critical', 'options' => ['placeholder' => '81']],
        //     ];
        // }

        return array_merge($a, $b, $c);
    }

    /**
     * Return the View for Assigning existing OIDs to the NetElementType or Parameter itself (children)
     *
     * @return  View
     */
    public function assign($id)
    {
        $model = \NamespaceController::get_model_name();
        $view_var = $model::find($id);
        $view_header = 'Attach single OIDs';
        $model_pure = \NamespaceController::get_route_name();
        $headline = BaseViewController::compute_headline($model_pure, $view_header, $view_var).' assign';

        // Get Mibs in case all OIDs from one Mib shall be attached
        $mibs = \Modules\HfcSnmp\Entities\MibFile::select(['id', 'name', 'version'])->get();
        $mibs = isset($mibs[0]) ? $mibs[0]->html_list($mibs, 'name', true) : [];

        // exclude mibs that don't have OIDs ??
        // foreach ($mibs as $mib)
        // {
        // 	if ($mib->oids)
        // 		$mibs_e[$mib->id] = $mib->name;
        // }

        // Get OIDs to Multiselect from
        $oids = [];
        $oids_raw = \Modules\HfcSnmp\Entities\OID::get(['id', 'name', 'oid']);
        foreach ($oids_raw as $key => $oid) {
            $oids[$oid->id] = $oid->name.' - '.$oid->oid;
        }

        return \View::make('hfcreq::NetElementType.assign', $this->compact_prep_view(compact('view_header', 'headline', 'view_var', 'oids', 'mibs', 'model_pure')));
    }

    /**
     * Attach OIDs to a NetElementType - Store in pivot/intermediate-table (parameter) - Selection is done in assign.blade.php
     * Attach SubOIDs to a Parameter - for exact table definitions
     *
     * Possible Methods:
     * Single Chosen OIDs via Multiselect
     * All OIDs from an already uploaded MibFile
     * A Newline-separated List of OIDs that have to exist in Database (from already uploaded MibFile)
     *
     * @param 	$id 			integer 	netelementtype id or parameter id
     * @input 	oid_id			array 		IDs of the OIDs we want to attach (transfered via HTTP POST)
     * @input 	mibfile_id 		integer 	ID of MIB-File
     * @input 	oid_list 		Text 		Newline-separated List of OIDs
     *
     * @author Nino Ryschawy
     */
    public function attach_oids($id)
    {
        // Selected MibFile
        if (Request::filled('mibfile_id')) {
            if (($mibfile_id = Request::input('mibfile_id')) == 0) {
                return \Redirect::back();
            }

            // generate list of OIDs and attach to device type (fastest method)
            $oids = \Modules\HfcSnmp\Entities\OID::where('mibfile_id', '=', $mibfile_id)->get(['id'])->keyBy('id')->keys()->all();
        }

        // List from Textarea
        if (Request::filled('oid_list')) {
            $delimiters = [',', ';', "\n"];
            $oid_list = str_replace($delimiters, $delimiters[0], Request::input('oid_list'));
            $oid_list = explode($delimiters[0], $oid_list);

            foreach ($oid_list as $oid) {
                $oid = trim($oid, "\r.0");
                $oid_o = \Modules\HfcSnmp\Entities\OID::where('oid', 'like', '%'.$oid)->get(['id'])->first();
                if ($oid_o) {
                    $oids[] = $oid_o->id;
                }
            }
        }

        // Multiselect
        if (Request::filled('oid_id')) {
            $oids = Request::input('oid_id');
        }

        // $devtype = NetElementType::findOrFail($id);
        // $devtype->oids()->attach($oids);

        $model = NamespaceController::get_route_name();

        if (isset($oids)) {
            $this->_create_parameter($id, $oids, $model);
        }

        // TODO: Implement Validation ?

        return \Redirect::route($model.'.edit', $id);
    }

    /**
     * Creates a Parameter related to NetElementType or a Child for the Parameter itself for each OID in the List
     *
     * @param 	id 		Integer 	NetElementType ID
     * @param 	oids 	Array 		List of OID IDs [0 => id1, 1 => id2, ...]
     * @param 	model 	String 		e.g.: NetElementType or Parameter
     */
    private function _create_parameter($id, $oids, $model)
    {
        foreach ($oids as $oid_id) {
            // $data = array(
            // 	'oid_id' => $oid_id,
            // 	'netelementtype_id' => $id,
            // 	);

            $data['oid_id'] = $oid_id;

            if ($model == 'NetElementType') {
                $data['netelementtype_id'] = $id;
            } else {
                $data['parent_id'] = $id;
            }

            \Modules\HfcSnmp\Entities\Parameter::create($data);
        }
    }

    /**
     * Detach all attached OIDs from a NetElementType
     */
    public function detach_all($id)
    {
        $model = NamespaceController::get_route_name();

        if ($model == 'NetElementType') {
            \Modules\HfcSnmp\Entities\Parameter::where('netelementtype_id', '=', $id)->delete();
        } else {
            \Modules\HfcSnmp\Entities\Parameter::where('parent_id', '=', $id)->delete();
        }

        return \Redirect::back();
    }
}
