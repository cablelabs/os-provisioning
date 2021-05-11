<?php

namespace Modules\ProvVoip\Http\Controllers;

use Request;
use Modules\ProvVoip\Entities\Mta;
use Modules\ProvBase\Entities\Modem;

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

        $mac = Request::get('mac', '');
        if ($mac === '') {
            $modem_id = Request::get('modem_id', 0);
            if (boolval($modem_id)) {
                $modem = Modem::find($modem_id);
                if ($modem) {
                    $mac = $modem->mac;
//                    Uncomment this block if you want to suggest mac address of next mta (if modem has already a mta)
//                    if($last_mta = $modem->mtas()->orderBy('updated_at', 'desc')->first()){
//                        $mac = $last_mta->mac;
//                    }
                    if ($mac) {
                        $dec_mac = hexdec($mac);
                        $dec_mac++;
                        $mac = rtrim(strtoupper(chunk_split(str_pad(dechex($dec_mac), 12, '0', STR_PAD_LEFT), 2, ':')), ':');
                    }
                }
            }
        }

        // label has to be the same like column in sql table
        // TODO: Type is without functionality -> hidden
        return [
            ['form_type' => 'text', 'name' => 'mac', 'description' => 'MAC Address', 'init_value' => $mac, 'options' => ['placeholder' => 'AA:BB:CC:DD:EE:FF'], 'help' => trans('helper.mac_formats')],
            ['form_type' => 'text', 'name' => 'hostname', 'description' => 'Hostname', 'options' => ['readonly']],
            ['form_type' => 'text', 'name' => 'modem_id', 'description' => 'Modem', 'hidden' => 1],
            ['form_type' => 'select', 'name' => 'configfile_id', 'description' => 'Configfile', 'value' => $this->_add_empty_first_element_to_options($model->html_list_with_count($model->configfiles(), 'name', false, '', 'configfile_id', 'mta')), 'help' => trans('helper.configfile_count')],

            // ATM there is only SIP
            /* array('form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => Mta::getPossibleEnumValues('type', false)), */
            ['form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => 'sip', 'hidden' => 1],
        ];
    }

    protected function prepare_input($data)
    {
        $data = parent::prepare_input($data);

        return unifyMac($data);
    }

    /**
     * Create tabs for Mta page.
     * See: BaseController native function for more information
     *
     * @param Modules\ProvVoip\Entities\Mta
     * @return array
     * @author Roy Schneider
     */
    protected function editTabs($model)
    {
        \Session::put('Edit', 'MTA');

        $tabs = parent::editTabs($model);
        $analysisTabs = $model->modem->analysisTabs();
        unset($analysisTabs[0]);

        return array_merge($tabs, $analysisTabs);
    }

    /**
     * Restart MTA via API
     *
     * @return JsonResponse
     *
     * @author Ole Ernst
     */
    public function api_restart($ver, $id)
    {
        if ($ver !== '0') {
            return response()->v0ApiReply(['messages' => ['errors' => ["Version $ver not supported"]]]);
        }

        $mta = static::get_model_obj()->findOrFail($id);
        $mta->restart();

        return response()->v0ApiReply([], true, $id);
    }

    public function prepare_rules($rules, $data)
    {
        $modem = Modem::where('id', $data['modem_id'])->with('configfile')->first();

        if ($modem->configfile->device == 'cm') {
            $id = $data['id'] ?? null;
            $rules['mac'][] = 'required';
            $rules['mac'][] = 'unique:mta,mac,'.$id.',id,deleted_at,NULL'; //|unique:mta,mac',
        }

        return parent::prepare_rules($rules, $data);
    }
}
