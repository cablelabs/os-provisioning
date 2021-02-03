<?php

namespace App\Observers;

use App\SupportRequest;

/**
 * SupportRequest Observer Class
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
        curl_setopt($ch, CURLOPT_URL, 'https://support.nmsprime.com/mail.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
