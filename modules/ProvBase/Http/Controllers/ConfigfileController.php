<?php

namespace Modules\ProvBase\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
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
        $cvc_files = Configfile::get_files('cvc');

        // label has to be the same like column in sql table

        $form = [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            ['form_type' => 'select', 'name' => 'device', 'description' => 'Device', 'value' => ['cm' => 'CM', 'mta' => 'MTA']],
            ['form_type' => 'select', 'name' => 'parent_id', 'description' => 'Parent Configfile',
                 'value' => $model->html_list(Configfile::where('id', '!=', $model->id)->get(), ['device', 'name'], true, ': '), ],
            ['form_type' => 'select', 'name' => 'public', 'description' => 'Public Use', 'value' => ['yes' => 'Yes', 'no' => 'No']],
            ['form_type' => 'textarea', 'name' => 'text', 'description' => 'Config File Parameters'],
            ['form_type' => 'select', 'name' => 'firmware', 'description' => 'Choose Firmware File', 'value' => $firmware_files],
            ['form_type' => 'file', 'name' => 'firmware_upload', 'description' => 'or: Upload Firmware File'],
            ['form_type' => 'select', 'name' => 'cvc', 'description' => 'Choose Certificate File', 'value' => $cvc_files, 'help' => $model->get_cvc_help()],
            ['form_type' => 'file', 'name' => 'cvc_upload', 'description' => 'or: Upload Certificate File'],
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

        // check and handle uploaded firmware and cvc files
        $this->handle_file_upload('firmware', '/tftpboot/fw/');
        $this->handle_file_upload('cvc', '/tftpboot/cvc/');

        // finally: call base method
        return parent::store();
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

        // session message if configfile had assigned cvc/firmware which doesn't exist
        if ($content['cvc'] != '' && ! file_exists('/tftpboot/cvc/'.$content['cvc'])) {
            \Session::push('tmp_warning_above_form', trans('messages.setManually', ['name' => $content['name'], 'file' => $content['cvc']]));
        }

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
        if (\Validator::make($configfile, $this->prepare_rules(Configfile::rules(), $configfile))->fails()) {
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
            // check and handle uploaded firmware and cvc files
            $this->handle_file_upload('firmware', '/tftpboot/fw/');
            $this->handle_file_upload('cvc', '/tftpboot/cvc/');

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
