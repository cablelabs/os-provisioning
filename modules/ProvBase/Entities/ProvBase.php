<?php

namespace Modules\ProvBase\Entities;

use GlobalConfig;
use File;

class ProvBase extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'provbase';

	public $name = 'Provisioning Basic Config';

	// Don't forget to fill this array
	protected $fillable = ['provisioning_server', 'ro_community', 'rw_community', 'domain_name', 'notif_mail', 'dhcp_def_lease_time', 'dhcp_max_lease_time', 'startid_contract', 'startid_modem', 'startid_endpoint'];

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'provisioning_server' => 'ip',
		);
	}

	// Name of View
	public static function get_view_header()
	{
		return 'Prov Base Config';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return "Prov Base";
	}

	/**
     * BOOT - init provbase observer
     */
    public static function boot()
    {
        parent::boot();

        ProvBase::observe(new ProvBaseObserver);
        ProvBase::observe(new \SystemdObserver);
    }


    public function make_dhcp_glob_conf()
    {
		$file_dhcp_conf = '/etc/dhcp/nms/global.conf';

		$data = 'ddns-domainname "'.$this->domain_name.'.";'."\n";
		$data .= 'option domain-name "'.$this->domain_name.'";'."\n";
		$data .= 'option domain-name-servers '.$this->provisioning_server.";\n";
		$data .= 'default-lease-time '.$this->dhcp_def_lease_time.";\n";
		$data .= 'max-lease-time '.$this->dhcp_max_lease_time.";\n";
		$data .= 'next-server '.$this->provisioning_server.";\n";
		$data .= 'option log-servers '.$this->provisioning_server.";\n";
		$data .= 'option time-servers '.$this->provisioning_server.";\n";

		$data .= "\n# zone\nzone ".$this->domain_name." {\n\tprimary ".$this->provisioning_server.";\n\tkey dhcpupdate;\n}\n";

		// provisioning server hostname encoding for dhcp
		$arr = explode('.', system('hostname'));
		$hostname = '';
		foreach ($arr as $value)
		{
			$nr = strlen($value);
			if ($nr < 10)
				$hostname .= "\\00$nr";
			else if ($nr < 100)
				$hostname .= "\\0$nr";
			else
				$hostname .= "\\$nr";
			$hostname .= $value;
		}
		$hostname .= '\\000';

		$data .= "\n# CLASS Specs for CM, MTA, CPE\n";
		$data .= 'class "CM" {'."\n\t".'match if (substring(option vendor-class-identifier,0,6) = "docsis");'."\n\toption ccc.dhcp-server-1 ".$this->provisioning_server.";\n}\n\n";
		$data .= 'class "MTA" {'."\n\t".'match if (substring(option vendor-class-identifier,0,4) = "pktc");'."\n\t".'option ccc.provision-server 0 "'.$hostname.'"; # number of letters before every through dot seperated word'."\n\t".'option ccc.realm 05:42:41:53:49:43:01:31:00;  # BASIC.1'."\n}\n\n";
		$data .= 'class "Client" {'."\n\t".'match if ((substring(option vendor-class-identifier,0,6) != "docsis") and (substring(option vendor-class-identifier,0,4) != "pktc"));'."\n\t".'spawn with option agent.remote-id; # create a sub-class automatically'."\n\t".'lease limit 4; # max 4 private cpe per cm'."\n}\n\n";
		$data .= 'class "Client-Public" {'."\n\t".'match if ((substring(option vendor-class-identifier,0,6) != "docsis") and (substring(option vendor-class-identifier,0,4) != "pktc"));'."\n\t".'match pick-first-value (option agent.remote-id);'."\n\t".'lease limit 4; # max 4 public cpe per cm'."\n}\n\n";

		File::put($file_dhcp_conf, $data);
    }
}


/**
 * ProvBase Observer Class
 * Handles changes on ProvBase Gateways
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ProvBaseObserver
{

    public function updated($model)
    {
        $model->make_dhcp_glob_conf();
    }

}