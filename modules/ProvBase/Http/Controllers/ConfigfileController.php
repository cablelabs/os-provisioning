<?php

namespace Modules\ProvBase\Http\Controllers;

use Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Modules\ProvBase\Entities\Modem;
use Illuminate\Support\Facades\Request;
use Modules\ProvBase\Entities\Configfile;

class ConfigfileController extends \BaseController
{
    protected $index_tree_view = true;

    protected $edit_view_second_button = true;
    protected $second_button_name = 'Export';
    protected $second_button_title_key = 'exportConfigfiles';

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new Configfile;
        }

        $firmware_files = Configfile::get_files('fw');

        // label has to be the same like column in sql table

        $form = [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            ['form_type' => 'select', 'name' => 'device', 'description' => 'Device', 'value' => ['cm' => 'CM', 'mta' => 'MTA', 'tr069' => 'TR-69']],
            ['form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Configfile',
                'value' => $model->html_list(Configfile::where('id', '!=', $model->id)->get(), ['device', 'name'], true, ': '), ],
            ['form_type' => 'select', 'name' => 'public', 'description' => 'Public Use', 'value' => ['yes' => 'Yes', 'no' => 'No']],
            ['form_type' => 'textarea', 'name' => 'text', 'description' => 'Config File Parameters'],
            ['form_type' => 'select', 'name' => 'firmware', 'description' => 'Choose Firmware File', 'value' => $firmware_files],
            ['form_type' => 'file', 'name' => 'firmware_upload', 'description' => 'or: Upload Firmware File'],

        ];

        if (\Route::currentRouteName() == 'Configfile.create') {
            array_push($form, ['form_type' => 'file', 'name' => 'import', 'description' => trans('messages.import'), 'help' => trans('messages.importTree')]);
        }

        return $form;
    }

    /**
     * Returns validation data array with correct device type for validation of config text
     *
     * @author Nino Ryschawy
     */
    public function prepare_rules($rules, $data)
    {
        $rules['text'] .= ':'.$data['device'];

        return $rules;
    }

    /**
     * Dont show export button on create page
     */
    public function create()
    {
        $this->edit_view_second_button = false;

        return parent::create();
    }

    /**
     * Overwrites the base method => we need to handle file uploads
     * @author Patrick Reichel
     */
    public function store($redirect = true)
    {
        if (Request::hasFile('import')) {
            $error = $this->importTree();
            \Session::push('tmp_error_above_form', $error);

            return redirect()->back();
        }

        // check and handle uploaded firmware files
        $this->handle_file_upload('firmware', '/tftpboot/fw/');

        // finally: call base method
        return parent::store();
    }

    public function searchDeviceParams($id)
    {
        $model = Configfile::find($id);

        $parametersArray = [];
        $storagefile = 'data/provbase/gacs/'.($model->id).'.json';
        //take from storage
        if (Storage::exists($storagefile)) {
            $parametersArray = json_decode(Storage::get($storagefile), true);
        }

        //if storage is empty, regenerate storage
        if (empty($parametersArray)) {
            $this->refreshGenieAcs($model->id, false);
            $parametersArray = json_decode(Storage::get($storagefile), true);
        }

        if (empty($parametersArray)) {
            return [];
        }

        $parametersArray = array_values($parametersArray);
        $returnArray = [];
        $search = $_GET['search'];
        if (empty($search)) {
            return [];
        }

        $elements = 0;
        foreach ($parametersArray as $param) {
            if (stristr($param['id'], $search)) {
                $returnArray[] = $param;
                $elements++;
                if ($elements > 50) {
                    break;
                }
            }
        }

        return json_encode($returnArray);
    }

    public function refreshGenieAcs($id, $refresh = true)
    {
        $model = Configfile::find($id);

        $query = [
            '_lastInform' => [
                '$gt' => \Carbon\Carbon::now('UTC')->subMinute(5)->toIso8601ZuluString(),
            ],
        ];

        $query = json_encode($query);
        $route = "devices/?query=$query&projection=_deviceId._SerialNumber";

        $online = array_map(function ($value) {
            return $value->_deviceId->_SerialNumber ?? null;
        }, json_decode(\modules\ProvBase\Entities\Modem::callGenieAcsApi($route, 'GET')));

        $modemSerials = $model->modem()->whereNotNull('serial_num')->distinct()->pluck('serial_num');

        $modemSerialsIntersect = array_values(array_intersect($modemSerials, $online));
        if (count($modemSerialsIntersect > 0)) {
            $modemSerial = $modemSerialsIntersect[0];
            $modem = $model->modem()->where('serial_num', $modemSerial)->first();
            $modem = Modem::first();
            \Modules\ProvMon\Http\Controllers\ProvMonController::realtimeTR069($modem, true);

            $modem = Modem::callGenieAcsApi("devices/?query={\"_deviceId._SerialNumber\":\"{$modemSerial}\"}", 'GET');
            $parametersArray = $this->buildElementList($this->getFromDevices($modem));

            $storagefile = 'data/provbase/gacs/'.($model->id).'.json';
            Storage::put($storagefile, json_encode($parametersArray));
        }

        if ($refresh) {
            return redirect()->back();
        }
    }

    /**
     * Returns content from devices.json
     *
     * @author Robin Sachse
     */
    public function getFromDevices($devicesContent)
    {
        if ($jsonDecode = json_decode($devicesContent, true)) {
            return reset($jsonDecode);
        }

        return [];
    }

    /**
     * Extracts all valid elements and their subelements from devices.json for
     * the current device and returns these as array
     *
     * @author Robin Sachse
     */
    public function buildElementList($devicesJson, $inPath = '')
    {
        // some devices do have "Device:" and others may have "InternetGatewayDevice:"
        $parametersArray = [];
        $tmpInPath = $inPath;
        if (! empty($tmpInPath) && substr($tmpInPath, 0, -1) != '.') {
            $tmpInPath .= '.';
        }
        foreach ($devicesJson as $key => $elementJson) {
            $inPath = $tmpInPath.$key;
            $parametersArray[] = ['id' => $inPath, 'name' => $inPath];
            if (is_array($elementJson)) {
                $parametersArray = array_merge($parametersArray, $this->buildElementList($elementJson, $inPath));
            }
        }

        return $parametersArray;
    }

    /**
     * Handles the output of a Drag&Drop interface below a tr096 config, if the
     * current config is used by at least one modem that is also known in
     * devices.json
     *
     * @author Robin Sachse
     */
    protected function getAdditionalDataForEditView($model)
    {
        if ($model->device != 'tr069') {
            return [];
        }

        $jsonFromDb = '{}';
        $searchFlag = '#monitoring:';

        foreach (explode("\n", $model->text) as $line) {
            if (substr($line, 0, strlen($searchFlag)) == $searchFlag) {
                $jsonFromDb = substr($line, strlen($searchFlag));
                break;
            }
        }

        $parametersArray = [];
        $storagefile = 'data/provbase/gacs/'.($model->id).'.json';
        // take from storage
        if (Storage::exists($storagefile)) {
            $parametersArray = json_decode(Storage::get($storagefile), true);
        }

        // if storage is empty, regenerate storage
        if (empty($parametersArray)) {
            $this->refreshGenieAcs($model->id, false);
            $parametersArray = json_decode(Storage::get($storagefile), true);
        }

        if (empty($parametersArray)) {
            return [];
        }

        $jsonArrayPage = [];
        $listCounter = 0;

        $jsonDecoded = json_decode($jsonFromDb, true);
        if ($jsonDecoded !== null) {
            foreach ($jsonDecoded as $jsName => $jsonArray) {
                $jsonArrayPage[$listCounter]['name'] = $jsName;
                foreach ($jsonArray as $jKey => $jElement) {
                    if (! is_array($jElement)) {
                        $jElement = [0 => $jElement, 1 => [0 => '+', 1 => null], 2 => [null,'+',null]];
                    }
                    if (count($jElement)>=1 && ! is_array($jElement[1])) {
                        $jElement[1]=[0 => '+', 1 => null];
                    }
                    if (count($jElement)>=2 && ! is_array($jElement[2])) {
                        $jElement[2]=[null,'+',null];
                    }
                    $jsonArrayPage[$listCounter]['content'][] = ['name' => $jKey, 'id' => $jElement[0], 'operator' => $jElement[1][0], 'opvalue' => $jElement[1][1], 'cvalue' => $jElement[2][0],'coperator' => $jElement[2][1],'copvalue' => $jElement[2][2]];
                }
                $listCounter++;
            }
        }

        array_unshift($jsonArrayPage, ['name' => 'listdevices', 'content' => array_slice(array_values($parametersArray), 0, 50)]);

        foreach ($jsonArrayPage as $jsonArray) {
            if (! array_key_exists('content', $jsonArray)) {
                $jsonArray['content'] = [];
            }
            $lists[] = $jsonArray;
        }

        return ['lists' => $lists, 'searchFlag' => $searchFlag];
    }

    /**
     * Generate tree of configfiles.
     *
     * @author Roy Schneider
     * @return void|string Void on Success, Error message on failure
     */
    public function importTree()
    {
        $import = File::get(Request::file('import'));
        $import = $this->replaceIds($import);
        $json = json_decode($import, true);

        if (! $json) {
            return trans('messages.invalidJson');
        }

        $this->recreateTree($json, Request::filled('name'), Configfile::pluck('name'));
    }

    /**
     * Replace all id's and parent_id's.
     *
     * @author Roy Schneider
     * @param string $content JSON String from uploaded File
     * @return string
     */
    public function replaceIds(string $content): string
    {
        // array of in file existing id's (id":number,)
        preg_match_all('/id":(\d+),/', $content, $importedIds);
        $importedIdStrings = array_unique($importedIds[0]);
        $importedIds = array_unique($importedIds[1]);
        sort($importedIdStrings, SORT_NATURAL);
        sort($importedIds, SORT_NATURAL);

        $maxId = Configfile::withTrashed()->max('id');
        $startId = $maxId ? ++$maxId : 1;
        $tempImportId = $startId + last($importedIds) + 1;

        if (strpos($content, 'id":'.$startId.',')) {
            $this->replaceDuplicateId($content, $importedIds, $importedIdStrings, $startId, $tempImportId);
            $tempImportId++;
        }

        foreach ($importedIdStrings as $key => $idString) {
            if (($startId + 1) === $importedIds[$key]) {
                $this->replaceDuplicateId($content, $importedIds, $importedIdStrings, $startId + 1, $tempImportId);
            }

            $content = str_replace($idString, 'id":'.$startId.',', $content);

            $startId++;
            $tempImportId++;
        }

        // Uploaded File has a Root CF
        if (Str::contains($content, 'parent_id":null,')) {
            return $content;
        }

        // if CF-subbranch, replace parent_id with parent_id of input
        preg_match_all('/parent_id":\d+,/', $content, $parentIds);

        return str_replace(array_shift($parentIds[0]), 'parent_id":'.Request::get('parent_id').',', $content);
    }

    /**
     * If the Imported Configfiles have IDs that are in the range of the new
     * created Configfiles the IDs are overwritten and cause DB errors. The
     * Ids get replaced with a higher number to prevent that.
     *
     * @param string $content   JSON string from uploaded file
     * @param array $importedIds[int]   Original ids from imported JSON as integer
     * @param array $importedIdStrings[string]  Strings with the ids that should be replaced
     * @param int $start    possible duplicate id
     * @param int $tempImportId     high id number that guarantees no conflict
     * @return void
     */
    protected function replaceDuplicateId(string &$content, array &$importedIds, array &$importedIdStrings, int $start, int $tempImportId): void
    {
        $content = str_replace('id":'.($start).',', 'id":'.($tempImportId).',', $content);

        $importedIds = array_map(function ($id) use ($start, $tempImportId) {
            return $id == $start ? $tempImportId : intval($id);
        }, $importedIds);

        $importedIdStrings = array_map(function ($idString) use ($start, $tempImportId) {
            return $idString === 'id":'.($start).',' ? 'id":'.($tempImportId).',' : $idString;
        }, $importedIdStrings);
    }

    /**
     * Recursively create all configfiles with related children.
     *
     * @author Roy Schneider
     * @param array $content    Current Configfile
     * @param bool $hasName     Take Name of Input field for first Configfile?
     * @param Illuminate\Support\Collection $originalConfigfiles    Data of all Configfiles
     * @return void
     */
    public function recreateTree(array $content, bool $hasName, \Illuminate\Support\Collection $originalConfigfiles): void
    {
        // see if this name already exists
        while ($originalConfigfiles->contains($content['name'])) {
            $content['name'] .= '(2)';
        }
        $originalConfigfiles->push($content['name']);

        // if there are no children
        if (! array_key_exists('children', $content)) {
            $this->checkAndSetContent($content, $hasName);

            return;
        }

        $children[] = array_pop($content);

        if ($this->checkAndSetContent($content, $hasName)) {
            return;
        }

        // session message if configfile had assigned firmware which doesn't exist
        if ($content['firmware'] != '' && ! file_exists('/tftpboot/fw/'.$content['firmware'])) {
            \Session::push('tmp_warning_above_form', trans('messages.setManually', ['name' => $content['name'], 'file' => $content['firmware']]));
        }

        // recursively for all children
        foreach ($children as $group) {
            foreach ($group as $child) {
                $this->recreateTree($child, false, $originalConfigfiles);
            }
        }
    }

    /**
     * Create configfiles or replace input if validation passes.
     *
     * @author Roy Schneider
     * @param array $configfile Config file data
     * @param bool $requestHasNameInput
     * @return bool
     */
    public function checkAndSetContent(array $configfile, bool $requestHasNameInput): bool
    {
        // CF-Form was not filled
        if (! $requestHasNameInput) {
            Configfile::create($configfile);

            return false;
        }

        Request::merge($configfile);
        Request::merge(['import' => 'import']);

        // only continue if the input would pass the validation
        $cf = new Configfile;
        $cf->id = $configfile['id'];

        if (\Validator::make($configfile, $this->prepare_rules($cf->rules(), $configfile))->fails()) {
            return true;
        }
    }

    /**
     * Overwrites the base method => we need to handle file uploads
     * @author Patrick Reichel
     */
    public function update($id)
    {
        if (! Request::filled('_2nd_action')) {
            // check and handle uploaded firmware files
            $this->handle_file_upload('firmware', '/tftpboot/fw/');

            // finally: call base method
            return parent::update($id);
        }

        $name = Configfile::find($id)->name;
        \Storage::put("tmp/$name", json_encode($this->exportTree($id, Configfile::get())));
        \Session::push('tmp_success_above_form', trans('messages.exportSuccess', ['name' => $name]));

        return response()->download('/var/www/nmsprime/storage/app/tmp/'.$name);
    }

    /**
     * Recursively creates an array of all configfiles with their children.
     * Note: takes about 7-10 ms per configfile
     *
     * @author Roy Schneider
     * @param int $id
     * @return array $tree
     */
    public function exportTree($id, $configfiles)
    {
        $model = $configfiles->where('id', $id)->first();
        $tree = $model->getAttributes();

        $children = $configfiles->where('parent_id', $id)->all();

        if (! empty($children)) {
            foreach ($children as $child) {
                $tree['children'][] = $this->exportTree($child->id, $configfiles);
            }
        }
        unset($tree['created_at'], $tree['updated_at'], $tree['deleted_at']);

        return $tree;
    }
}
