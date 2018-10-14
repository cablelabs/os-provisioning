<?php

namespace Modules\ProvVoip\Http\Controllers;

use Modules\ProvVoip\Entities\Mta;
use Modules\ProvBase\Entities\Modem;
use Illuminate\Support\Facades\Input;
use Modules\ProvBase\Entities\Configfile;

class MtaController extends \BaseController
{
    protected $index_create_allowed = false;
    protected $save_button_name = 'Save / Restart';

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new Mta;
        }

        $mac = Input::get('mac', '');
        if ($mac === '') {
            $modem_id = Input::get('modem_id', 0);
            if (boolval($modem_id)) {
                $modem = Modem::find($modem_id);
                if ($modem) {
                    $mac = $modem->mac;
//                    Uncomment this block if you want to suggest mac address of next mta (if modem has already a mta)
//                    if($last_mta = $modem->mtas()->orderBy('updated_at', 'desc')->first()){
//                        $mac = $last_mta->mac;
//                    }
                    $dec_mac = hexdec($mac);
                    $dec_mac++;
                    $mac = rtrim(strtoupper(chunk_split(dechex($dec_mac), 2, ':')), ':');
                }
            }
        }

        // label has to be the same like column in sql table
        // TODO: Type is without functionality -> hidden
        return [
            ['form_type' => 'text', 'name' => 'mac', 'description' => 'MAC Address', 'init_value' => $mac, 'options' => ['placeholder' => 'AA:BB:CC:DD:EE:FF'], 'help' => trans('helper.mac_formats')],
            ['form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly']],
            ['form_type' => 'text', 'name' => 'modem_id', 'description' => 'Modem', 'hidden' => 1],
            ['form_type' => 'select', 'name' => 'configfile_id', 'description' => 'Configfile', 'value' => $this->_add_empty_first_element_to_options($model->html_list($model->configfiles(), 'name'))],

            // ATM there is only SIP
            /* array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => Mta::getPossibleEnumValues('type', false)), */
            ['form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => 'sip', 'hidden' => 1],
        ];
    }

    protected function prepare_input_post_validation($data)
    {
        return unify_mac($data);
    }
}
