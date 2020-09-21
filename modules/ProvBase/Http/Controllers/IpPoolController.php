<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\IpPool;
use App\Http\Controllers\BaseViewController;

class IpPoolController extends \BaseController
{
    protected $index_create_allowed = false;

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $init_values = [];

        if (! $model) {
            $model = new IpPool;
        }

        $types = BaseviewController::translateArray([
            'CM' => 'Cable Modem',
            'CPEPriv' => 'CPE Private',
            'CPEPub' => 'CPE Public',
            'MTA' => 'MTA',
        ]);

        $typesKeys = array_keys($types->all());

        // create context: calc next free ip pool
        if (! $model->exists) {
            $init_values = [];

            // parse which ip type we want to create
            $type = (isset($_GET['type']) ? $_GET['type'] : 'CM');

            // get last ip pools ordered desc by net, which means get last added ip pool
            $ippools = IpPool::where('type', '=', $type)->orderBy(\DB::raw('INET_ATON(net)'), 'DESC')->get();

            // check if we still have pools in DB
            if ($ippools->count() > 0) {
                // create the next free ip net: this means:
                // next_net = last_broadcast + 1
                $last_ippool = $ippools[0];
                $next_net = long2ip(ip2long($last_ippool->broadcast_ip) + 1);

                // next dns is most likly last dns
                $init_values['dns1_ip'] = $last_ippool->dns1_ip;
                $init_values['dns2_ip'] = $last_ippool->dns2_ip;
                $init_values['dns3_ip'] = $last_ippool->dns3_ip;
            } else {
                switch ($type) { // if not: add default net, depending on type
                case 'CM':      $next_net = env('IP_CM_DEFAULT_NET', '10.0.0.0'); break;
                case 'CPEPriv': $next_net = env('IP_CPE_PRIV_DEFAULT_NET', '100.64.0.0'); $init_values['dns1_ip'] = '8.8.8.8'; break;
                case 'CPEPub':  $next_net = env('IP_CPE_PUB_DEFAULT_NET', '192.168.100.0'); $init_values['dns1_ip'] = '8.8.8.8'; break;
                case 'MTA':     $next_net = env('IP_MTA_DEFAULT_NET', '100.96.0.0'); $init_values['dns1_ip'] = \Modules\ProvBase\Entities\ProvBase::first()->provisioning_server; break;
                default: $next_net = '192.168.200.0'; break;
            }
            }

            // Get default IP net size, like 255.255.255.0 = /24
            switch ($type) {
                case 'CM':      $size = env('IP_CM_DEFAULT_SIZE', 19); break; // /19
                case 'CPEPriv': $size = env('IP_CPE_PRIV_DEFAULT_SIZE', 22); break; // /22
                case 'CPEPub':  $size = env('IP_CPE_PUB_DEFAULT_SIZE', 27); break; // /27
                case 'MTA':     $size = env('IP_MTA_DEFAULT_SIZE', 24); break; // /24
                default: $size = 24; break;
            }

            // calc the ip next net
            $sub = new \IPv4\SubnetCalculator($next_net, $size);

            $init_values += [
                'net' => $sub->getNetworkPortion(),
                'netmask' => $sub->getSubnetMask(),
                'ip_pool_start' => long2ip(ip2long($sub->getIPAddressRange()[0]) + 1), // first ip + 1
                'ip_pool_end' => long2ip(ip2long($sub->getIPAddressRange()[1]) - 2), // last ip -2
                'router_ip' => long2ip(ip2long($sub->getIPAddressRange()[1]) - 1), // last ip -1
                'broadcast_ip' => $sub->getBroadcastAddress(),
            ];
        }

        // label has to be the same like column in sql table
        $ret_tmp = [
            ['form_type' => 'select', 'name' => 'netgw_id', 'description' => 'NetGw Hostname', 'value' => $model->html_list($model->netgw_hostnames(), 'hostname'), 'hidden' => 1],
            ['form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => $types,
                'options' => ['translate' => true], 'help' => trans('provbase::help.type'),
                'select' => array_combine($typesKeys, $typesKeys), ],
            ['form_type' => 'text', 'name' => 'net', 'description' => trans('provbase::view.net')],
            ['form_type' => 'text', 'name' => 'netmask', 'description' => 'Netmask', 'options' => ['placeholder' => '255.255.0.0 | /16']],
            ['form_type' => 'text', 'name' => 'ip_pool_start', 'description' => 'First IP'],
            ['form_type' => 'text', 'name' => 'ip_pool_end', 'description' => 'Last IP'],
            ['form_type' => 'ip', 'name' => 'router_ip', 'description' => 'Router IP'],
            ['form_type' => 'text', 'name' => 'broadcast_ip', 'description' => 'Broadcast IP'],
            ['form_type' => 'text', 'name' => 'dns1_ip', 'description' => 'DNS1 IP'],
            ['form_type' => 'text', 'name' => 'dns2_ip', 'description' => 'DNS2 IP'],
            ['form_type' => 'text', 'name' => 'dns3_ip', 'description' => 'DNS3 IP'],
            ['form_type' => 'text', 'name' => 'prefix', 'description' => 'Prefix (IPv6)', 'select' => 'CPEPub'],
            ['form_type' => 'text', 'name' => 'prefix_len', 'description' => 'Prefix length (IPv6)', 'select' => 'CPEPub'],
            ['form_type' => 'text', 'name' => 'delegated_len', 'description' => 'Delegated length (IPv6)', 'select' => 'CPEPub'],
            ['form_type' => 'textarea', 'name' => 'optional', 'description' => 'Additional Options'],
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];

        // add init values if set
        $ret = [];
        foreach ($ret_tmp as $elem) {
            if (array_key_exists($elem['name'], $init_values)) {
                $elem['init_value'] = $init_values[$elem['name']];
            }
            array_push($ret, $elem);
        }

        return $ret;
    }

    /**
     * Replaces the placeholders (named like the array key inside the data array/sql columns)
     * in the rules array with the needed data of the data array;
     *
     * used in own validation
     *
     * @author Nino Ryschawy
     */
    public function prepare_rules($rules, $data)
    {
        // Replace placeholder in rules by data e.g. net in ip_pool_start rule by $data['net']
        $rulesWithPlaceholders = ['ip_pool_start', 'ip_pool_end', 'router_ip', 'broadcast_ip'];
        foreach ($rulesWithPlaceholders as $rkey) {
            $description = $rules[$rkey];

            foreach ($data as $key => $value) {
                if (($pos = strpos($description, $key)) && substr($description, $pos - 1, 1) != '|') {
                    $rules[$rkey] = $description = preg_replace("/$key\b/", "$value", $description);
                }
            }
        }

        // Add ip rule with version dependent on net to the beginning
        $rulesToAddIpRule = array_merge($rulesWithPlaceholders, ['net', 'dns1_ip', 'dns2_ip', 'dns3_ip', 'prefix']);
        foreach ($rulesToAddIpRule as $rkey) {
            $rule = $data['version'] ? 'ipv'.$data['version'] : 'ip';
            $rules[$rkey] = isset($rules[$rkey]) ? $rule.'|'.$rules[$rkey] : $rule;
        }

        if ($data['version'] == '6') {
            $rules['type'] = 'In:CPEPub';

            foreach (['prefix', 'prefix_len', 'delegated_len'] as $rkey) {
                $rules[$rkey] .= '|required';
            }
        }

        return $rules;
    }

    public function prepare_input($data)
    {
        $data['version'] = self::getVersion($data['net']);

        // Convert cidr netmask for IPv4
        if ($data['version'] == '4' && self::isCidrNotation($data['netmask'])) {
            $data['netmask'] = IpPool::cidrToMask($data['netmask']);
        }

        $fields = ['net', 'netmask', 'ip_pool_start', 'ip_pool_end', 'router_ip', 'broadcast_ip', 'dns1_ip', 'dns2_ip', 'dns3_ip'];
        foreach ($fields as $key) {
            $data[$key] = str_replace(' ', '', $data[$key]);
        }

        return parent::prepare_input($data);
    }

    /**
     * @return mixed    string 4|6 or null if no valid ip
     */
    public static function getVersion($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return '6';
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return '4';
        }
    }

    /**
     * Convert netmask in CIDR notation to dotted decimal notation
     *
     * @param string
     * @return string
     */
    public function cidrToMask($netmask)
    {
        $int = str_replace('/', '', $netmask);

        return long2ip(-1 << (32 - (int) $int));
    }
}
