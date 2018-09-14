<?php

namespace App;

class SupportRequest extends BaseModel
{
    public $table = 'supportrequest';

    public static function rules($id = null)
    {
        return [
            'mail' => 'email',
            // 'license' => '',
        ];
    }

    public static function view_headline() : string
    {
        return 'Support Request';
    }

    // View Icon
    public static function view_icon() : string
    {
        return '<i class="fa fa-user-circle text-info"></i>';
    }

    // link title in index view
    public function view_index_label()
    {
        return 'Support Request';
    }

    /**
     * BOOT - init SupportRequestObserver
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new SupportRequestObserver);
        self::observe(new \App\SystemdObserver);
    }

    /**
     * Get System state in preparation of support request
     *
     * @return array
     */
    public static function system_status()
    {
        $services = ['dhcpd', 'xinetd', 'ntpd', 'named', 'firewalld'];
        foreach ($services as $service) {
            exec("systemctl status $service", $out[$service]);
        }

        // routes, ip
        exec('/usr/sbin/ip r', $out['routes']);
        exec('/usr/sbin/ip a', $out['ip']);

        // thumb of modems
        $out['modem_statistic'] = \Modules\Dashboard\Http\Controllers\DashboardController::get_modem_statistics();
        foreach (['text', 'state', 'fa'] as $key) {
            unset($out['modem_statistic']->$key);
        }

        return $out;
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
class SupportRequestObserver
{
    public function creating($supportrequest)
    {
        // get extra info to store from global config
        $sla = \App\Sla::first();
        $supportrequest->sla_name = $sla->name;

        $this->send_mail($sla, $supportrequest);
    }

    // send mail to appropriate address xs|pending|sla|nosla
    public function send_mail($sla, $supportrequest)
    {
        $destination = 'nosla';
        if ($sla->valid()) {
            $destination = 'sla';
        } elseif (\Session::has('klicked_sla')) {
            $destination = 'sla-pending';
            \Session::forget('klicked_sla');
        }

        // Mail is sent internally and triggered via http post
        $data_arr = [
            'destination'   => $destination,
            'supportrequest' => $supportrequest->getDirty(),
            'system_status' => SupportRequest::system_status(),
            ];

        $data = http_build_query($data_arr);

        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, 'https://nms10.erznet.tv/mail.php');
        curl_setopt($ch, CURLOPT_URL, 'https://192.168.0.174:9876/mail.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_POST ,count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
