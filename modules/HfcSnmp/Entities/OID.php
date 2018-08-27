<?php

namespace Modules\HfcSnmp\Entities;

/**
 * The OID Model with it's OID and Properties from MibFile (access, type, description,...) and html properties for WebGUI View
 *
 * Type can have the following Values
 * i 	INTEGER
 * u 	unsigned INTEGER
 * t 	TIMETICKS
 * a 	IPADDRESS
 * o 	OBJID
 * s 	STRING
 * x 	HEX STRING
 * d 	DECIMAL STRING
 * n 	NULLOBJ
 * b 	BITS
 */
class OID extends \BaseModel
{
    public $table = 'oid';

    // Add your validation rules here
    public static function rules($id = null)
    {
        return [
            'oid' => 'required',
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'OID';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-opera"></i>';
    }

    // AJAX Index list function
    // generates datatable content and classes for model
    public function view_index_label()
    {
        $bsclass = $this->get_bsclass();

        return ['table' => $this->table,
                'index_header' => [$this->table.'.name', $this->table.'.name_gui',  $this->table.'.oid', $this->table.'.access'],
                'header' =>  $this->name.' - '.$this->oid,
                'order_by' => ['2' => 'asc'], ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        if ($this->access == 'read-only') {
            $bsclass = 'danger';
        }

        return $bsclass;
    }

    /**
     * Relations
     */
    public function mibfile()
    {
        return $this->belongsTo('Modules\HfcSnmp\Entities\MibFile');
    }

    // public function netelementtypes()
    // {
    // 	return $this->belongsToMany('Modules\HfcReq\Entities\NetElementType', 'netelementtype_oid', 'oid_id', 'netelementtype_id');
    // }

    public function parameters()
    {
        // NOTE: This should be done with eager loading if not already done by laravel automatically, because oid relation is needed close to all the time
        return $this->HasMany('Modules\HfcSnmp\Entities\Parameter', 'oid_id');
        // ->with('Modules\HfcSnmp\Entities\OID')->get();
    }

    public function view_belongs_to()
    {
        return $this->mibfile;
    }

    /**
     * Return OID List for Select Field
     */
    public static function oid_list($empty_elem = false)
    {
        $list = $empty_elem ? [0 => null] : [];

        // Note: DB::table is way faster than instantiating the Eloquent Objects which are not needed in this case
        $oids = \DB::table('oid')->whereNull('deleted_at')->get(['id', 'name', 'name_gui', 'oid']);

        foreach ($oids as $oid) {
            $list[$oid->id] = $oid->name_gui ?: $oid->name;
            $list[$oid->id] .= ' - '.$oid->oid;
        }

        return $list;
    }

    /**
     * Return The Select Values for the Parameter in the NetElement Controlling View
     *
     * @return array
     */
    public function get_select_values()
    {
        if ($this->value_set) {
            // create list
            $separator_1 = [',', ' ', ';'];
            $separator_2 = ['=', ':'];

            $pairs = str_replace($separator_1, $separator_1[0], $this->value_set);
            $pairs = explode($separator_1[0], $pairs);

            foreach ($pairs as $value) {
                $key_value = str_replace($separator_2, $separator_2[0], $value, $cnt);

                if (! $cnt) {
                    // Mib-format valuename(value)
                    $key_value = [];

                    $key_value[0] = substr($value, 0, $x = strpos($value, '('));
                    $key = substr($value, $x + 1);
                    $key_value[1] = substr($key, 0, strlen($key) - 1);
                } else {
                    $key_value = explode($separator_2[0], $key_value);
                }

                // discard empty strings caused by spaces
                if ($key_value[0]) {
                    $list[$key_value[1]] = $key_value[0];
                }
            }

            return $list;
        }

        if ($this->endvalue) {
            $this->stepsize = $this->stepsize ?: 1;
            $arr = range($this->startvalue, $this->endvalue, $this->stepsize);

            return array_combine($arr, $arr);

            return $list;
        }

        return [];
    }

    /**
     * Return SNMP OID Type Character from Syntax String (for OID Type field)
     *
     * @return string 	Enum for OIDs SNMP Type
     */
    public static function get_oid_type($string)
    {
        if (strpos($string, 'unsigned integer') !== false) {
            return 'u';
        } elseif (strpos($string, 'unsigned32') !== false) {
            return 'u';
        } elseif (strpos($string, 'integer') !== false) {
            return 'i';
        } elseif (strpos($string, 'decimal string') !== false) {
            return 'd';
        } elseif (strpos($string, 'hex string') !== false) {
            return 'x';
        } elseif (strpos($string, 'string') !== false) {
            return 's';
        } elseif (strpos($string, 'counter') !== false) {
            return 'i';
        } elseif (strpos($string, 'timeticks') !== false) {
            return 't';
        } elseif (strpos($string, 'ipaddress') !== false) {
            return 'a';
        } elseif (strpos($string, 'bits') !== false) {
            return 'b';
        }

        return '';
    }

    /**
     * Return value set from Syntax field of OID or Start & Endvalue
     *
     * @param 	string 			Syntax field
     * @return 	string|array
     */
    public static function get_value_set($string)
    {
        if (($x = strpos($string, '{')) !== false) {
            // value_set
            return substr($string, $x + 1, strlen($string) - $x - 2);
        }

        if (($x = strpos($string, '(')) !== false) {
            // start & end value
            $y = strpos($string, '..');
            $startval = substr($string, $x + 1, $y - $x - 1);
            $endval = substr($string, $y + 2, strlen($string) - 1);

            return [$startval, $endval];
        }
    }
}
