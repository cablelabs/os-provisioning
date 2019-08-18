<?php

namespace Modules\HfcSnmp\Entities;

class MibFile extends \BaseModel
{
    public $table = 'mibfile';

    public $guarded = ['mibfile_upload'];

    /**
     * @Const MibFile Upload Path relativ to storage directory
     */
    const REL_MIB_UPLOAD_PATH = 'app/data/hfcsnmp/mibs/';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'filename' => 'unique:mibfile,filename,'.$id.',id,deleted_at,NULL',
        ];
    }

    /**
     * View specific Stuff
     */
    // Name of View
    public static function view_headline()
    {
        return 'MIB-File';
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
        $bsclass = $this->oids()->count() ? 'success' : 'info';

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
        return $this->hasMany('Modules\HfcSnmp\Entities\OID', 'mibfile_id')->orderBy('oid');
    }

    /**
     * Boot: init observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new MibFileObserver);
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
     * @return  0  on success, Redirect::back Object on Error
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

            $x = is_string($value_set) ? strpos($syntax, '{') : strpos($syntax, '(');
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
                'html_type' 	=> is_string($value_set) ? 'select' : 'text',
                'value_set'		=> is_string($value_set) ? $value_set : null,
                'startvalue' 	=> is_string($value_set) ? null : $value_set[0],
                'endvalue' 		=> is_string($value_set) ? null : $value_set[1],
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

class MibFileObserver
{
    public function created($mibfile)
    {
        // create oids was moved to MibFileController@store for better responses on errors
    }

    public function deleting($mibfile)
    {
        // hard delete OIDs as Database becomes huge otherwise
        // $mibfile->hard_delete_oids();

        // TODO: Unlink file ?? - better not -> in case related mibs need this mib the user must not load it again
    }
}
