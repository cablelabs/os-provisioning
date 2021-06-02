<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
