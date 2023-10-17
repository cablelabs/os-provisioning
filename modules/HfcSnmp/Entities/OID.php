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
    public function rules()
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
        return ['table' => $this->table,
            'bsclass' => $this->get_bsclass(),
            'index_header' => [$this->table.'.name', $this->table.'.name_gui',  $this->table.'.oid', $this->table.'.access'],
            'header' => $this->label(),
        ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        if ($this->access == 'read-only') {
            $bsclass = 'danger';
        }

        return $bsclass;
    }

    public function label()
    {
        return $this->oid.' - '.$this->name.($this->name_gui ? " ({$this->name_gui})" : '');
    }

    /**
     * Relations
     */
    public function mibfile()
    {
        return $this->belongsTo(MibFile::class);
    }

    public function parameters()
    {
        // NOTE: This should be done with eager loading if not already done by laravel automatically, because oid relation is needed close to all the time
        return $this->hasMany(Parameter::class, 'oid_id');
        // ->with('Modules\HfcSnmp\Entities\OID')->get();
    }

    public function view_belongs_to()
    {
        return $this->mibfile;
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
            $this->stepsize = floatval($this->stepsize) == 0 ? 1 : floatval($this->stepsize);
            $arr = range($this->startvalue, $this->endvalue, $this->stepsize);

            return array_combine($arr, $arr);
        }

        return [];
    }

    /**
     * Return SNMP OID Type Character from Syntax String (for OID Type field)
     *
     * @return string|null Enum for OIDs SNMP Type
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
    }

    /**
     * Return value set from Syntax field of OID or Start & Endvalue
     *
     * @param 	string 			Syntax field
     * @return string|array
     */
    public static function get_value_set($string)
    {
        if (($x = strpos($string, '{')) !== false) {
            // value_set
            return substr($string, $x + 1, strlen($string) - $x - 2);
        }

        // NOTE: This skips handling of special cases - TODO: handle them and check via # grep -R "SYNTAX" .snmp/mibs/
        if (($x = strpos($string, '(')) === false || strpos($string, '|') !== false || ($y = strpos($string, '..')) === false) {
            return;
        }

        // start & end value
        $startval = substr($string, $x + 1, $y - $x - 1) ?: null;
        $endval = substr($string, $y + 2, strlen($string) - $y - 3) ?: null;

        return [$startval, $endval];
    }
}
