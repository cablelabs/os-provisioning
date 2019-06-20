<?php

namespace Modules\ProvBase\Entities;

class Endpoint extends \BaseModel
{
    // The associated SQL table for this Model
    public $table = 'endpoint';

    public static function rules($id = null)
    {
        return [
            'mac' => 'required|mac|unique:endpoint,mac,'.$id.',id,deleted_at,NULL',
            'hostname' => 'regex:/^[0-9A-Za-z\-]+$/|required|unique:endpoint,hostname,'.$id.',id,deleted_at,NULL',
            'ip' => 'required|ip|unique:endpoint,ip,'.$id.',id,deleted_at,NULL',
        ];
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
        if ($this->fixed_ip && $this->ip) {
            $header = "$this->hostname ($this->mac / $this->ip)";
        } else {
            $header = "$this->hostname ($this->mac)";
        }

        return ['table' => $this->table,
                'index_header' => [$this->table.'.hostname', $this->table.'.mac', $this->table.'.ip', $this->table.'.description'],
                'header' =>  $header,
                'bsclass' => $bsclass, ];
    }

    public function get_bsclass()
    {
        $bsclass = 'success';

        return $bsclass;
    }

    public function view_belongs_to()
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

    public function nsupdate($del = false)
    {
        $cmd = '';
        $zone = ProvBase::first()->domain_name;

        if ($del) {
            if ($this->getOriginal()['fixed_ip'] && $this->getOriginal()['ip']) {
                $rev = implode('.', array_reverse(explode('.', $this->getOriginal()['ip'])));
                $cmd .= "update delete {$this->getOriginal()['hostname']}.cpe.$zone.\nsend\n";
                $cmd .= "update delete $rev.in-addr.arpa.\nsend\n";
            } else {
                $mangle = exec("echo \"{$this->getOriginal()['mac']}:{$this->modem->mac}\" | tr -cd '[:xdigit:]' | xxd -r -p | openssl dgst -sha256 -macopt hexkey:$(cat /etc/named-ddns-cpe.key) -binary | python -c 'import base64; import sys; print(base64.b32encode(sys.stdin.read())[:6].lower())'");
                $cmd .= "update delete {$this->getOriginal()['hostname']}.cpe.$zone.\nsend\n";
                $cmd .= "update delete $mangle.cpe.$zone.\nsend\n";
            }
        } else {
            if ($this->fixed_ip && $this->ip) {
                // endpoints with a fixed-address will get an A and PTR record (ip <-> hostname)
                $rev = implode('.', array_reverse(explode('.', $this->ip)));
                $cmd .= "update add $this->hostname.cpe.$zone. 3600 A $this->ip\nsend\n";
                $cmd .= "update add $rev.in-addr.arpa. 3600 PTR $this->hostname.cpe.$zone.\nsend\n";
            } else {
                // other endpoints will get a CNAME and PTR record (mangle <-> hostname)
                // mangle name is based on cm and cpe mac
                $mangle = exec("echo \"$this->mac:{$this->modem->mac}\" | tr -cd '[:xdigit:]' | xxd -r -p | openssl dgst -sha256 -macopt hexkey:$(cat /etc/named-ddns-cpe.key) -binary | python -c 'import base64; import sys; print(base64.b32encode(sys.stdin.read())[:6].lower())'");
                $cmd .= "update add $this->hostname.cpe.$zone. 3600 CNAME $mangle.cpe.$zone.\nsend\n";
                $cmd .= "update add $mangle.cpe.$zone. 3600 PTR $this->hostname.cpe.$zone.\nsend\n";
            }
        }

        $pw = env('DNS_PASSWORD');
        $handle = popen("/usr/bin/nsupdate -v -l -y dhcpupdate:$pw", 'w');
        fwrite($handle, $cmd);
        pclose($handle);
    }

    /**
     * BOOT:
     * - init modem observer
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new EndpointObserver);
        self::observe(new \App\SystemdObserver);
    }

    /**
     * Make DHCP config files for EPs
     */
    public static function make_dhcp()
    {
        $dir = '/etc/dhcp-nmsprime/';
        $file_ep = $dir.'endpoints-host.conf';

        $data = '';

        foreach (self::all() as $ep) {
            $data .= "host $ep->hostname { hardware ethernet $ep->mac; ";
            if ($ep->fixed_ip && $ep->ip) {
                $data .= "fixed-address $ep->ip; ";
            }
            $data .= "}\n";
        }

        $ret = file_put_contents($file_ep, $data, LOCK_EX);
        if ($ret === false) {
            die('Error writing to file');
        }

        // chown for future writes in case this function was called from CLI via php artisan nms:dhcp that changes owner to 'root'
        system('/bin/chown -R apache /etc/dhcp-nmsprime/');

        return $ret > 0;
    }
}

class EndpointObserver
{
    public function creating($endpoint)
    {
        if (! $endpoint->fixed_ip) {
            $endpoint->ip = null;
        }
    }

    public function created($endpoint)
    {
        $endpoint->make_dhcp();
        Cmts::make_dhcp_conf_all();
        $endpoint->nsupdate();
    }

    public function updating($endpoint)
    {
        if (! $endpoint->fixed_ip) {
            $endpoint->ip = null;
        }
        $endpoint->nsupdate(true);
    }

    public function updated($endpoint)
    {
        $endpoint->make_dhcp();
        Cmts::make_dhcp_conf_all();
        $endpoint->nsupdate();
    }

    public function deleted($endpoint)
    {
        $endpoint->make_dhcp();
        Cmts::make_dhcp_conf_all();
        $endpoint->nsupdate(true);
    }
}
