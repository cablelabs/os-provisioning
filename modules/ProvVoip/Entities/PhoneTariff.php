<?php

namespace Modules\ProvVoip\Entities;

class PhoneTariff extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'phonetariff';

    // Add your validation rules here
    public static function rules($id = null)
    {
        // Port unique in the appropriate mta (where mta_id=mta_id and deleted_at=NULL)

        return [
            'external_identifier' => 'required',
            'name' => 'required|unique:phonetariff,name,'.$id.',id,deleted_at,NULL',
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
                'index_header' => [$this->table.'.name', $this->table.'.type', $this->table.'.description', $this->table.'.voip_protocol', $this->table.'.usable'],
                'bsclass' => $bsclass,
                'header' => $this->name.' ('.$this->type.')', ];
    }

    // Name of View
    public static function get_view_header()
    {
        return 'PhoneTariffs';
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
        return self::__get_tariffs('purchase');
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
        return self::__get_tariffs('sale');
    }

    /**
     * Return a tariff for a given type.
     *
     * @author Patrick Reichel
     *
     * @param $type The tariff type as string (currently purchase and sale).
     *
     * @return array with phonetariff.id=>phonetariff.name
     */
    private static function __get_tariffs($type)
    {
        $supported_types = ['purchase', 'sale'];

        $ret = [];

        // check if valid type is given
        if (! in_array($type, $supported_types)) {
            throw new \InvalidArgumentException('Type must be in ['.implode(', ', $supported_types).']');
        }

        // can be used in raw statement; $type is well known and not given from user input
        $tariffs = self::where('type', $type)->where('usable', 1)->get();

        foreach ($tariffs as $tariff) {
            $ret[$tariff->id] = $tariff->name;
        }

        return $ret;
    }
}
