<?php

namespace Modules\ProvBase\Entities;

use GlobalConfig;
use File;

class ProvBase extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'provbase';

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
        // ProvBase::observe(new \SystemdObserver);
    }


    public function make_dhcp_glob_conf()
    {
		$file_dhcp_conf = '/etc/dhcp/nms/global.conf';

		$data = 'ddns-domainname "'.$this->domain_name.'.";'."\n";
		$data .= 'option domain-name "'.$this->domain_name.'";'."\n";
		$data .= 'option domain-name-servers '.$this->provisioning_server.";\n";
		$data .= 'default-lease-time '.$this->dhcp_def_lease_time.";\n";
		$data .= 'max-lease-time '.$this->dhcp_max_lease_time.";\n";

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