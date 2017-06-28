<?php

namespace Modules\HfcSnmp\Entities;

class SnmpValue extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'snmpvalue';

    // Disable Observer by Default as this would leed to a mass number of log entries - NOTE: Observer is enabled in SnmpController for manual SnmpSets
    public $observer_enabled = false;

	// Add your validation rules here
    public static function rules($id = null)
    {
        return array(
        );
    }

    // Name of View
    public static function view_headline()
    {
        return 'Temporary Testing SNMP Values';
    }

    // link title in index view
    public function view_index_label()
    {
        $device = '';
        if ($this->device)
            $device = $this->device->name;

        $snmpmib = '';
        if ($this->snmpmib)
            $snmpmib = $this->snmpmib->field;

        return ['index' => [$device, $snmpmib, $this->oid_index, $this->value],
                'index_header' => ['Device Type', 'SNMP MIB Reference', 'SNMP OID Index', 'Value'],
                'header' => $this->id.': '.$device.' - '.$snmpmib.' - '.$this->oid_index];
    }

    /**
     * Relations
     */
    public function netelement()
    {
        return $this->belongsTo('Modules\HfcReq\Entities\NetElement');
    }

    public function oid()
    {
        return $this->belongsTo('Modules\HfcSnmp\Entities\OID');
    }

}