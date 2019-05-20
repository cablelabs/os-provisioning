<?php

namespace App;

class Sla extends BaseModel
{
    public $table = 'sla';

    /**
     * @var array  Name/Size of existing service level agreements
     */
    public static $names = ['s', 'm', 'l', 'xl', 'xxl'];

    /**
     * @var array  Conditions of existing service level agreements
     */
    public static $conditions = [
        'total outage' => ['time' => '24x7', 'Response time' => '<b><2h</b>', 'RT without SLA' => '<i class="fa fa-times fa-lg text-danger" title="undefined"></i>'],
        'critical' => ['time' => '9to5', 'Response time' => '<b><3d</b> (typical same day)', 'RT without SLA' => '<i class="fa fa-times fa-lg text-danger" title="undefined"></i>'],
        'normal' => ['time' => '9to5', 'Response time' => '<b><2w</b> (typical < 3 days)', 'RT without SLA' => '<i class="fa fa-times fa-lg text-danger" title="undefined"></i>'],
        ];

    public static $threshholds = [
            'xs' => ['modems' => 10, 'contracts' => 40, 'cmts' => 1, 'netelements' => 10],
            's' => ['modems' => 100, 'contracts' => 400, 'cmts' => 1, 'netelements' => 10],
            'm' => ['modems' => 500, 'contracts' => 2000, 'cmts' => 2, 'netelements' => 50],
            'l' => ['modems' => 1000, 'contracts' => 4000, 'cmts' => 4, 'netelements' => 100],
            'xl' => ['modems' => 2500, 'contracts' => 10000, 'cmts' => 6, 'netelements' => 250],
            'xxl' => ['modems' => 5000, 'contracts' => 20000, 'cmts' => 10, 'netelements' => 500],
            ];

    public static function rules($id = null)
    {
        return [
            // 'name' => '',
            // 'license' => '',
        ];
    }

    public static function view_headline() : string
    {
        return 'SLA';
    }

    // View Icon
    public static function view_icon() : string
    {
        return '<i class="fa fa-user-circle text-info"></i>';
    }

    // link title in index view
    public function view_index_label()
    {
        return 'SLA';
    }

    public function set_sla_dependent_values()
    {
        if (\Module::collections()->has('ProvBase')) {
            $this->num_cmts = \Modules\ProvBase\Entities\Cmts::count();
            $this->num_contracts = \Modules\ProvBase\Entities\Contract::count();
            $this->num_modems = \Modules\ProvBase\Entities\Modem::count();
        }

        if (\Module::collections()->has('HfcReq')) {
            $this->num_netelements = \Modules\HfcReq\Entities\NetElement::count();
        }
    }

    /**
     * Return the actual SLA size based on the real size of the network
     * also if no SLA is set at all
     *
     * @return string of [xs | s | m | l | xl | xxl]
     */
    public function get_sla_size()
    {
        $this->set_sla_dependent_values();

        foreach (array_reverse(self::$threshholds) as $size => $table) {
            foreach ($table as $key => $value) {
                if ($this->{'num_'.$key} >= $value) {
                    return $size;
                }
            }
        }

        return 'xs';
    }

    /**
     * Determine if Service Level Agreement is valid for this system
     *
     * @return bool
     */
    public function valid()
    {
        if (! in_array($this->name, self::$names)) {
            return false;
        }

        $this->set_sla_dependent_values();

        foreach (self::$threshholds[$this->name] as $key => $value) {
            if ($this->{'num_'.$key} >= $value) {
                return false;
            }
        }

        return true;
    }
}
