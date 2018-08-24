<?php

namespace App\Console\Commands;

/**
 * Holds some helper methods used by several updater console commands
 * currently those related to envia TEL API.
 *
 * @author Patrick Reichel
 */
trait DatabaseUpdaterTrait
{
    /**
     * Update an order (using a curl request against the given URL.
     * Since updating uses the same functionality as updating via frontend we accessing the cron method in ProvVoipEnviaController using cURL.
     *
     * This may be not the best way – but the one without bigger refactoring of the sources…
     * TODO: Evaluate other solutions…
     *
     * @author Patrick Reichel
     *
     * @param $url URL to be accessed by cURL
     */
    protected function _perform_curl_request($url)
    {
        $ch = curl_init();

        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,	// no valid cert for “localhost” – so we don't check
            CURLOPT_RETURNTRANSFER => true,		// return result instead of instantly printing to screen
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,	// resolve to IPv4 address
        ];

        curl_setopt_array($ch, $opts);

        $res = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code != 200) {
            \Log::error('HTTP error '.$http_code.' occured in scheduled updating of envia orders calling '.$url);
        }

        curl_close($ch);

        return $res;
    }
}
