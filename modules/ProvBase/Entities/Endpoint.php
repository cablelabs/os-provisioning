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
            'hostname' => 'required|regex:/^(?!cm-)(?!mta-)[0-9A-Za-z\-]+$/|unique:endpoint,hostname,'.$id.',id,deleted_at,NULL',
            'ip' => 'nullable|required_if:fixed_ip,1|ip|unique:endpoint,ip,'.$id.',id,deleted_at,NULL',
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
        return $this->belongsTo(Modem::class);
    }

    public function nsupdate($del = false)
    {
        $cmd = '';
        $zone = ProvBase::first()->domain_name;

        if ($del) {
            if ($this->getOriginal('fixed_ip') && $this->getOriginal('ip')) {
                $rev = implode('.', array_reverse(explode('.', $this->getOriginal('ip'))));
                $cmd .= "update delete {$this->getOriginal('hostname')}.cpe.$zone.\nsend\n";
                $cmd .= "update delete $rev.in-addr.arpa.\nsend\n";
            } else {
                $mangle = exec("echo '{$this->getOriginal('mac')}' | tr -cd '[:xdigit:]' | xxd -r -p | openssl dgst -sha256 -mac hmac -macopt hexkey:$(cat /etc/named-ddns-cpe.key) -binary | python -c 'import base64; import sys; print(base64.b32encode(sys.stdin.read())[:6].lower())'");
                $cmd .= "update delete {$this->getOriginal('hostname')}.cpe.$zone.\nsend\n";
                $cmd .= "update delete $mangle.cpe.$zone.\nsend\n";
            }
        } else {
            if ($this->fixed_ip && $this->ip) {
                // endpoints with a fixed-address will get an A and PTR record (ip <-> hostname)
                $rev = implode('.', array_reverse(explode('.', $this->ip)));
                $cmd .= "update add $this->hostname.cpe.$zone. 3600 A $this->ip\nsend\n";
                $cmd .= "update add $rev.in-addr.arpa. 3600 PTR $this->hostname.cpe.$zone.\nsend\n";
                if ($this->add_reverse) {
                    $cmd .= "update add $rev.in-addr.arpa. 3600 PTR $this->add_reverse.\nsend\n";
                }
            } else {
                // other endpoints will get a CNAME record (hostname -> mangle)
                // mangle name is based only on cpe mac address
                $mangle = exec("echo '$this->mac' | tr -cd '[:xdigit:]' | xxd -r -p | openssl dgst -sha256 -mac hmac -macopt hexkey:$(cat /etc/named-ddns-cpe.key) -binary | python -c 'import base64; import sys; print(base64.b32encode(sys.stdin.read())[:6].lower())'");
                $cmd .= "update add $this->hostname.cpe.$zone. 3600 CNAME $mangle.cpe.$zone.\nsend\n";
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
        self::reserveAddress($endpoint);

        $endpoint->make_dhcp();
        NetGw::make_dhcp_conf_all();
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
        self::reserveAddress($endpoint);

        $endpoint->make_dhcp();
        NetGw::make_dhcp_conf_all();
        $endpoint->nsupdate();
    }

    public function deleted($endpoint)
    {
        self::reserveAddress($endpoint);

        $endpoint->make_dhcp();
        NetGw::make_dhcp_conf_all();
        $endpoint->nsupdate(true);
    }

    /**
     * Handle changes of reserved ip addresses based on endpoints
     * This is called on created/updated/deleted in Endpoint observer
     *
     * @author Ole Ernst
     */
    private static function reserveAddress($endpoint)
    {
        // delete radreply containing Framed-IP-Address
        $endpoint->modem->radreply()->delete();

        // reset state of original ip address
        RadIpPool::where('framedipaddress', $endpoint->getOriginal('ip'))
            ->update(['expiry_time' => null, 'username' => '']);

        if ($endpoint->deleted_at || ! $endpoint->ip || ! $endpoint->modem->isPPP()) {
            return;
        }

        // add new radreply
        $reply = new RadReply;
        $reply->username = $endpoint->modem->ppp_username;
        $reply->attribute = 'Framed-IP-Address';
        $reply->op = ':=';
        $reply->value = $endpoint->ip;
        $reply->save();

        // set expiry_time to 'infinity' for reserved ip addresses
        RadIpPool::where('framedipaddress', $endpoint->ip)
            ->update(['expiry_time' => '9999-12-31 23:59:59', 'username' => $endpoint->modem->ppp_username]);
    }
}
