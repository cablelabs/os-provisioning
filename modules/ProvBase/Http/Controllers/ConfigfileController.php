<?php

namespace Modules\ProvBase\Http\Controllers;

use Illuminate\Support\Facades\Input;
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
        // TODO: type is without functionality -> hidden

        $form = [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name'],
            ['form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => ['generic' => 'generic', 'network' => 'network', 'vendor' => 'vendor', 'user' => 'user'], 'hidden' => 1],
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
        // check and handle uploaded firmware and cvc files
        $this->handle_file_upload('firmware', '/tftpboot/fw/');
        $this->handle_file_upload('cvc', '/tftpboot/cvc/');
        $error = $this->importTree();

        if ($error) {
            \Session::push('tmp_error_above_form', $error);

            return redirect()->back();
        }

        // finally: call base method
        return parent::store();
    }

    /**
     * Generate tree of configfiles.
     *
     * @author Roy Schneider
     * @return mixed values
     */
    public function importTree()
    {
        if (! Input::hasFile('import')) {
            return;
        }

        $content = $this->replaceIds(\File::get(Input::file('import')));

        $json = json_decode($content, true);

        if (! $json) {
            return trans('messages.invalidJson');
        }

        $this->recreateTree($json, Input::get()['name'] == '' ? true : false, Configfile::all()->pluck('name'));
    }

    /**
     * Replace all id's and parent_id's.
     *
     * @author Roy Schneider
     * @param string $content
     * @return string
     */
    public function replaceIds($content)
    {
        // array of in file existing id's (id":number,)
        preg_match_all('/[i][d]["][:]\d+[,]/', $content, $importedIds);
        $importedIds = array_unique($importedIds[0]);
        sort($importedIds, SORT_NATURAL);

        $maxId = Configfile::withTrashed()->max('id');

        $startId = $maxId ? ++$maxId : 0;

        foreach ($importedIds as $id) {
            $content = str_replace($id, 'id":'.$startId.',', $content);
            $startId++;
        }

        // replace first parent_id with parent_id of input
        preg_match_all('/[a-t]{6}.[d-i]{2}["][:]\d+[,]/', $content, $ids);
        $parentId = array_shift($ids[0]);
        $input = Input::get('parent_id');

        return str_replace($parentId, 'parent_id":'.$input.',', $content);
    }

    /**
     * Recursively create all configfiles with related children.
     *
     * @author Roy Schneider
     * @param array $content
     * @param bool $hasName
     */
    public function recreateTree($content, $hasName, $originalConfigfiles)
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
     * @param array $content
     * @param bool $hasName
     * @return bool
     */
    public function checkAndSetContent($content, $hasName)
    {
        if (! $hasName) {
            Configfile::create($content);

            return;
        }

        Input::merge($content);
        Input::merge(['import' => 'import']);

        // only continue if the input would pass the validation
        if (\Validator::make($content, $this->prepare_rules(Configfile::rules(), $content))->fails()) {
            return true;
        }
    }

    /**
     * Overwrites the base method => we need to handle file uploads
     * @author Patrick Reichel
     */
    public function update($id)
    {
        if (! Input::has('_2nd_action')) {
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
        $tree = $model['attributes'];

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
