<?php

namespace Modules\ProvBase\Entities;

use File;
use Modules\ProvBase\Entities\Cmts;

class Endpoint extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'endpoint';

	public static function rules($id = null)
	{
		return array(
			'mac' => 'required|mac|unique:endpoint,mac,'.$id.',id,deleted_at,NULL',
			'hostname' => 'required|unique:endpoint,hostname,'.$id.',id,deleted_at,NULL',
			'ip' => 'ip|unique:endpoint,ip,'.$id.',id,deleted_at,NULL',
		);
	}


	// Name of View
	public static function view_headline()
	{
		return 'Endpoints';
	}

	// View Icon
	public static function view_icon()
	{
		return '<i class="fa fa-map-marker"></i>';
	}

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label()
	{
		$bsclass = $this->get_bsclass();
		if ($this->fixed_ip && $this->ip)
			$header = "$this->hostname ($this->mac / $this->ip)";
		else
			$header = "$this->hostname ($this->mac)";

		return ['table' => $this->table,
				'index_header' => [$this->table.'.hostname', $this->table.'.mac', $this->table.'.ip', $this->table.'.description'],
				'header' =>  $header,
				'bsclass' => $bsclass];
	}

	public function get_bsclass()
	{
		$bsclass = 'success';

		return $bsclass;
	}

	public function view_belongs_to ()
	{
		return $this->modem;
	}

	/**
	 * all Relations:
	 */
	public function modem()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Modem');
	}



	/**
	 * BOOT:
	 * - init modem observer
	 */
	public static function boot()
	{
		parent::boot();

		Endpoint::observe(new EndpointObserver);
		Endpoint::observe(new \App\SystemdObserver);
	}

	/**
	 * Make DHCP config files for EPs
	 */
	public static function make_dhcp ()
	{
		$dir = '/etc/dhcp/nmsprime/';
		$file_ep = $dir.'endpoints-host.conf';

		$data = '';

		foreach (Endpoint::all() as $ep) {
			$data .= "host $ep->hostname { hardware ethernet $ep->mac; ";
			if ($ep->fixed_ip && $ep->ip)
				$data .= "fixed-address $ep->ip; ";
			$data .= "}\n";
		}

		$ret = File::put($file_ep, $data);
		if ($ret === false)
			die("Error writing to file");

		// chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
		system('/bin/chown -R apache /etc/dhcp/');

		return $ret > 0;
	}

}


class EndpointObserver {

	public function created($endpoint)
	{
		$endpoint->make_dhcp();
		Cmts::make_dhcp_conf_all();
	}

	public function updated($endpoint)
	{
		$endpoint->make_dhcp();
		Cmts::make_dhcp_conf_all();
	}

	public function deleted($endpoint)
	{
		$endpoint->make_dhcp();
		Cmts::make_dhcp_conf_all();
	}
}
