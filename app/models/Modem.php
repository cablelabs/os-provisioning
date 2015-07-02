<?php

class Modem extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		'hostname' => 'required|string'
		// 'contract_id' => 'required|integer',
		// 'mac' => 'required',
		// 'network_access' => 'required|boolean',
		// 'contract_id' => 'integer'
	];

	// Don't forget to fill this array
	protected $fillable = ['hostname', 'contract_id', 'mac', 'status', 'network_access', 'serial_num', 'inventar_num', 'description', 'parent'];

    public static function boot()
    {
        parent::boot();

        Modem::observe(new ModemObserver);
    }
}

class ModemObserver {

    public function updated($modem)
    {
        exec("logger \"update on Modem with ID \"".$modem->id);
    }


}