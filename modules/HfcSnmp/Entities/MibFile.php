<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\HfcSnmp\Entities;

class MibFile extends \BaseModel
{
    public $table = 'mibfile';

    public $guarded = ['mibfile_upload'];

    protected $withCount = [
        'oids',
    ];

    /**
     * @Const MibFile Upload Path relativ to storage directory
     */
    public const REL_MIB_UPLOAD_PATH = 'app/data/hfcsnmp/mibs/';

    // Add your validation rules here
    public function rules()
    {
        return [
            'filename' => 'unique:mibfile,filename,'.($this->id ?: 0).',id,deleted_at,NULL',
        ];
    }

    /**
     * View specific Stuff
     */
    // Name of View
    public static function view_headline()
    {
        return 'MibFile';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-file-o"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        return ['table' => $this->table,
            'index_header' => [$this->table.'.id', $this->table.'.name',  $this->table.'.version'],
            'header' =>  $this->name,
            'bsclass' => $this->get_bsclass(),
            'order_by' => ['1' => 'asc'],
        ];
    }

    public function get_bsclass()
    {
        $bsclass = $this->oids_count ? 'success' : 'info';

        return $bsclass;
    }

    public function view_has_many()
    {
        $ret['Edit']['OID']['class'] = 'OID';
        $ret['Edit']['OID']['relation'] = $this->oids;

        return $ret;
    }

    //Overwrite from BaseModel to add version
    // public function html_list($array, $column, $empty_option = false)
    // {
    // 	$ret[0] = null;

    // 	foreach ($array as $a)
    // 		$ret[$a->id] = $a->{$column}.'--'.$a->version;

    // 	return $ret;
    // }

    /**
     * Relations
     */
    public function oids()
    {
        return $this->hasMany(OID::class, 'mibfile_id')->orderByRaw('LENGTH(oid)')->orderBy('oid');
    }

    public function get_full_filepath()
    {
        return storage_path(self::REL_MIB_UPLOAD_PATH).$this->filename;
        // return storage_path(self::REL_MIB_UPLOAD_PATH).$this->name.'_'.$this->version.'.mib';
    }

    /**
     * Create OID Database Entries from parsing snmptranslate outputs of all OIDs of the MIB
     * Extract informations of OID: name, syntax, access, type, values, description, ...
     *
     * @return0 on success, Redirect::back Object on Error
     *
     * @author Nino Ryschawy
     */
    public function create_oids()
    {
        $abs_filepath = $this->get_full_filepath();
        // $abs_filepath = \Request::file('mibfile_upload')->path(); 		// if still in /tmp
        // $filetext = file_get_contents($abs_filepath);

        // check necessary? - Note: exception is bad response for user of running/production system
        if (! is_file($abs_filepath)) {
            return $this->_error('Upload File not yet written', 'Filesystem Error');
        }

        // Get all OIDs of MIB - this includes many OIDs from the MIBs that are included in this MIB
        exec("snmptranslate -To -m $abs_filepath 2>&1", $oids); 			// 2>&1 ... stderr to stdout

        // check if Translation of MIB is dependent of another MIB
        if (isset($oids[1]) && strpos($oids[1], 'Cannot find module') !== false) {
            preg_match('#\((.*?)\)#', substr($oids[1], 18), $mib);
            $msg = trans('messages.upload_dependent_mib_err', ['name' => $mib[1]]);

            return $this->_error($msg);
        }

        // Parse and Create all OIDs that really belong to this MIB
        foreach ($oids as $oid) {
            $out = [];
            $error = false;
            $name = $syntax = $type = $access = $description = '';

            // $out = shell_exec("snmptranslate -Td -m $abs_filepath $oid");
            exec("snmptranslate -Td -m $abs_filepath $oid", $out);

            if (! isset($out[0])) {
                continue;
            }

            // check if OID belongs to current uploaded MIB-File (exclude OIDs from included MIBs)
            if ($this->name != substr($out[0], 0, strpos($out[0], '::'))) {
                continue;
            }

            foreach ($out as $key => $line) {
                // name
                if ($key == 1) {
                    $tmp = explode(' ', $line);
                    if ($tmp[1] != 'OBJECT-TYPE') {
                        $error = true;
                        break;
                    }
                    $name = $tmp[0];
                }

                // syntax
                if (strpos($line, 'SYNTAX') !== false) {
                    $tmp = explode("\t", $line);
                    if (isset($tmp[1])) {
                        $syntax = trim($tmp[1]);
                    } else {
                        break;
                    }
                }

                // if ($name == 'vgpConfigurationICS2Nominal' && $key == 4)
                // 	d($out, $value_set, $syntax, $type);

                // access
                if (strpos($line, 'MAX-ACCESS') !== false) {
                    $tmp = explode("\t", $line);
                    if (isset($tmp[1])) {
                        $access = trim($tmp[1]);
                    } else {
                        break;
                    }
                }

                // description
                if (strpos($line, 'DESCRIPTION') !== false) {
                    $tmp = implode($out);
                    if (($end = strpos($tmp, 'DEFVAL')) === false) {
                        if (($end = strpos($tmp, '::=')) === false) {
                            $end = null;
                        }
                    }
                    $description = substr($tmp, 14, $end ? $end - 14 : null);
                    $description = str_replace("\t", '', $description);

                    break;
                }

                // TODO: try to extract unit divisor, value_set if not yet available from description ?

                unset($out[$key]);
            }

            if ($error || ! $access) {
                continue;
            }

            // determine if OID is a table

            // extract type & value_set (or start/endvalue) from syntax
            $type = OID::get_oid_type(strtolower($syntax));
            $value_set = OID::get_value_set($syntax);

            $x = is_array($value_set) ? strpos($syntax, '(') : strpos($syntax, '{');
            if ($x !== false) {
                $syntax = substr($syntax, 0, $x);
            }

            // create OID
            OID::create([
                'mibfile_id' 	=> $this->id,
                'oid' 			=> $oid,
                'name'	 		=> $name,
                'access'	 	=> $access,
                'syntax' 		=> $syntax,
                'type' 			=> $type,
                'oid_table' 	=> ($tab = preg_match('/[a-z][a-zA-Z0-9]*Table$/', $name)) ? $tab : 0,
                'html_type' 	=> is_array($value_set) ? 'text' : 'select',
                'value_set'		=> is_array($value_set) ? null : $value_set,
                'startvalue' 	=> is_array($value_set) ? $value_set[0] : null,
                'endvalue' 		=> is_array($value_set) ? $value_set[1] : null,
                'description' 	=> $description,
            ]);
        }

        return 0;
    }

    /**
     * Return Error Message without exception as their message is not seen on production Systems
     */
    private function _error($message, $error = 'Missing Dependency')
    {
        $this->delete();

        //return \Redirect::back()->with('message', $message)->with('message_color', 'red');
        // throw new \Exception($message);
        return \View::make('errors.generic')->with('message', $message)->with('error', $error);
    }

    /*
     * Recursive Deletion of related OIDs - use this function as generic recursive deletion is super slow in this case!
     *
     * NOTE: generic recursive Deletion was disabled in BaseModel@get_all_children() by added exceptional column name: mibfile_id
         * recursive deletion is enabled again because it's needed also for Parameters
     */
    // public function hard_delete_oids()
    // {
    // 	foreach($this->oids as $oid)
    // 		\DB::statement('DELETE from parameter WHERE oid_id='.$oid->id);

    // 	\DB::statement('DELETE from oid WHERE mibfile_id='.$this->id);
    // }
}
