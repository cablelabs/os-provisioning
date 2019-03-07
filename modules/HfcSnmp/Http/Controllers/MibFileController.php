<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Modules\HfcSnmp\Entities\MibFile;

class MibFileController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new MibFile;
        }

        // label has to be the same like column in sql table
        return [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'Name', 'hidden' => 'C'],
            ['form_type' => 'text', 'name' => 'filename', 'description' => 'Filename', 'hidden' => 'E', 'options' => ['readonly'], 'help' => trans('helper.mib_filename')],
            ['form_type' => 'text', 'name' => 'version', 'description' => 'Version | Revision', 'hidden' => 'C'],
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
            ['form_type' => 'file', 'name' => 'mibfile_upload', 'description' => 'Upload MIB-File', 'hidden' => 'E'],

        ];
    }

    /**
     * NOTE: Default Input is created directly in overwritten Store function because we need these Infos for fileupload there
     */
    // protected function prepare_input($data)
    // {
    // if (!\Str::contains(\Route::getCurrentRoute()->getName(), 'store'))
    // 	return parent::prepare_input($data);
    // }

    /**
     * Overwrite the base method to handle file uploads and Set Default Input here
     *
     * Intention: Automate all tasks that have to be done to Create OIDs by Uploading a MIB (least effort for User)
     * NOTE:
     * We extract Name and Revision from MibFile as Default Input here to Set Filename to Name_Revision.mib like it is saved in Storage
     * If this filename already exists we check md5sum of uploaded against existent file and create another file if hash differs
     *
     * @author Nino Ryschawy
     */
    public function store($redirect = true)
    {
        if (! \Request::hasFile('mibfile_upload')) {
            return parent::store();
        }

        $name = $version = $description = '';
        $error = $multiple = false;
        $tmp_filepath = \Request::file('mibfile_upload')->getPathName();
        $mib = file($tmp_filepath);
        // $abs_filepath = $mibfile->get_full_filepath();

        // Parse MIB file to get Name & Revision
        foreach ($mib as $line) {
            // name
            if (strpos($line, 'DEFINITIONS ::= BEGIN') !== false) {
                $tmp = explode(' ', trim($line));
                $name = $tmp[0];
            }

            // std revision format
            preg_match('/[0-9]{10,12}[Z]/', $line, $match);
            if (isset($match[0])) {
                $year_digits = strlen($match[0]) == 13 ? 4 : 2;
                $date = substr($match[0], 0, $year_digits).'-'.substr($match[0], $year_digits, 2).'-'.substr($match[0], $year_digits + 2, 2);

                $version = $match[0];
                $description .= $description ? "\n" : '';
                $description .= 'REVISION-DATE: '.$date;
                break;
            }

            // revision non standard
            if (strpos($line, 'REVISION') !== false || strpos($line, 'LAST-UPDATED') !== false) {
                $tmp = str_replace([' ', "\t"], '', $line);
                $start = 12;
                $tmp = substr($tmp, $start);

                $version .= $tmp;
                break;
            }
        }

        // Set Filename and check if File already exists
        $filename = $name.'_'.$version.'.mib';
        $full_targetfilepath = storage_path(MibFile::REL_MIB_UPLOAD_PATH).$filename;
        // $same = false;
        $i = 1;

        // check if MIB with same Name & Revision exists but with different content - then we add another number after version to filename
        while (is_file($full_targetfilepath)) {
            // NOTE: this break leads to validation error because file already exists - besides: upload is not necessary
            if (md5_file($full_targetfilepath) == md5_file($tmp_filepath)) {
                $error = true;
                break;
            }

            // Get version from existing file and compare?
            // $existing = MibFile::where('filename', '=', $filename)->get(['version'])->first();
            // $same = $same ? : $version == $existing->version;

            $i++;
            $filename = $name.'_'.$version.$i.'.mib';
            $full_targetfilepath = storage_path(MibFile::REL_MIB_UPLOAD_PATH).$filename;
            $multiple = true;
        }

        // Multiple occurences of MIBs with same Name can cause errors on translation or using SNMPD - how does SNMPD handle this ???
        if ($multiple) {
            $description .= "\nThis MIB already exists with same Name";
        }
        // if ($same)
        // 	$description .= " and Revision!";

        // Set Input Default Values
        \Request::merge([
            'name' 		=> $name,
            'filename'  => $filename,
            'version' 	=> $version,
            'description' => $description,
            ]);

        // MOVE file to Storage (if necessary)
        if (! $error) {
            $this->handle_file_upload(null, null);
        }

        $id = parent::store(false);

        // validation failed -> return redirect
        if (is_object($id)) {
            return $id;
        }

        // add OIDs to MibFile
        $mib = MibFile::find($id);
        $ret = $mib->create_oids();

        if ($ret) {
            return $ret;
        }

        return \Redirect::route('MibFile.edit', $id)->with('message', trans('messages.created'))->with('message_color', 'blue');
    }

    /**
     * Overwrite the base method to extend saved filename in storage by version
     * This is done to allow same MIBs with different versions to be stored
     */
    protected function handle_file_upload($base_field, $dst_path)
    {
        $filename = \Request::get('filename');
        // $filename = \Request::get('name').'_'.\Request::get('version').'.mib';

        $dir = storage_path(MibFile::REL_MIB_UPLOAD_PATH);

        if (! is_dir($dir)) {
            mkdir($dir, 0744, true);
        }

        \Request::file('mibfile_upload')->move($dir, $filename);
    }
}
