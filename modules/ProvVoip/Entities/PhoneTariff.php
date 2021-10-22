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

namespace Modules\ProvVoip\Entities;

class PhoneTariff extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'phonetariff';

    // Add your validation rules here
    public function rules()
    {
        // Port unique in the appropriate mta (where mta_id=mta_id and deleted_at=NULL)

        return [
            'external_identifier' => 'required',
            'name' => 'required|unique:phonetariff,name,'.($this->id ?: 0).',id,deleted_at,NULL',
            'usable' => 'required|boolean',
        ];
    }

    // Name of View
    public static function view_headline()
    {
        return 'Phone tariffs';
    }

    // View Icon
    public static function view_icon()
    {
        return '<i class="fa fa-phone-square"></i>';
    }

    // link title in index view
    public function view_index_label()
    {
        if (boolval($this->usable)) {
            $bsclass = 'success';
        } else {
            $bsclass = 'danger';
        }

        return ['table' => $this->table,
            'index_header' => [$this->table.'.name', $this->table.'.type', $this->table.'.external_identifier', $this->table.'.description', $this->table.'.voip_protocol', $this->table.'.usable'],
            'bsclass' => $bsclass,
            'header' => $this->name.' ('.$this->type.')', ];
    }

    // Name of View
    public static function get_view_header()
    {
        return 'PhoneTariff';
    }

    /**
     * Returns all purchase tariffs that are flagged as usable.
     *
     * @author Patrick Reichel
     *
     * @return array with phonetariff.id=>phonetariff.name
     */
    public static function get_purchase_tariffs()
    {
        return self::__get_tariffs(['purchase', 'basic']);
    }

    /**
     * Returns all sales tariffs that are flagged as usable.
     *
     * @author Patrick Reichel
     *
     * @return array with phonetariff.id=>phonetariff.name
     */
    public static function get_sale_tariffs()
    {
        return self::__get_tariffs(['sale', 'basic', 'landlineflat', 'allnetflat']);
    }

    /**
     * Return a tariff for a given type.
     *
     * @author Patrick Reichel
     *
     * @param $type The tariff type as string (currently purchase and sale).
     * @return array with phonetariff.id=>phonetariff.name
     */
    private static function __get_tariffs($types)
    {
        $supported_types = ['purchase', 'sale', 'basic', 'landlineflat', 'allnetflat'];

        $ret = [];

        // check if valid type is given
        foreach ($types as $type) {
            if (! in_array($type, $supported_types)) {
                throw new \InvalidArgumentException('Type must be in ['.implode(', ', $supported_types).']');
            }
        }

        // can be used in raw statement; $type is well known and not given from user input
        $tariffs = self::whereIn('type', $types)->where('usable', 1)->get();

        foreach ($tariffs as $tariff) {
            $ret[$tariff->id] = $tariff->name;
        }

        return $ret;
    }
}
