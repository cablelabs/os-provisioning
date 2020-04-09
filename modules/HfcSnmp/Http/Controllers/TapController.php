<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Log;
use Request;
use NamespaceController;
use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;
use App\Http\Controllers\BaseViewController;
use Modules\ProvMon\Http\Controllers\ProvMonController;

class TapController extends \BaseController
{
    const relCookiePath = 'app/tmp/cookie';

	public function show($id)
    {
        $netelement = NetElement::where('id', $id)->with('clusterObj')->first();

        $lineNr = $netelement->clusterObj ? $netelement->clusterObj->rkm_line_number : null;

        // Init View
        $view_header = trans('hfcreq::view.tapControlling').': '.$netelement->name;
        $view_var = $netelement;
        $route_name = NamespaceController::get_route_name();
        $headline = BaseViewController::compute_headline($route_name, $view_header, $view_var).' > controlling';
        $tabs = ProvMonController::checkNetelementtype($netelement);
        $hfcBaseConf = \Modules\HfcBase\Entities\HfcBase::first();

        $view_path = 'hfcreq::NetElement.tapControlling';

        return \View::make($view_path, $this->compact_prep_view(compact('view_var', 'view_header', 'tabs', 'route_name', 'headline', 'hfcBaseConf', 'lineNr')));
    }

    public function switchTapState()
    {
        Log::debug(__FUNCTION__);

        if ($this->loginNecessary()) {
            if (! $this->login()) {
                return 'Login error';
            }
        }

        return $this->switchState();
    }

    /**
     * Switch Sat-Kabel-Video-Encoder line to the requested number or to automatic switching
     *
     * NOTE: No login required with HTTP base authentication
     *
     * @return string - 'OK' on success
     */
    public function switchVideoLine()
    {
        $hfcBaseConf = \Modules\HfcBase\Entities\HfcBase::first();
        $line = Request::get('line');

        if ($line == 'auto') {
            // Turn on auto switching of line
            $url = 'http://'.$hfcBaseConf->video_controller.'/ajax?t=1&c=0xFF';
        } else {
            // send parameter with random value to bypass caching server
            $url = 'http://'.$hfcBaseConf->video_controller.'/ajax?t=1&c=0x01&p='.$line.'&'.rand(100, 999);
        }
        // Get line
        // $url = 'http://'.$hfcBaseConf->video_controller.'/ajax?t=1&c=0x02';

        Log::info('Set Sat-Kabel-Video-Encoder line to '.$line);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_USERPWD => $hfcBaseConf->video_controller_username.':'.$hfcBaseConf->video_controller_password,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $ret = curl_exec($ch);

        // Returns '1:0x01=187' or '1:0xFF=187'
        if (strpos($ret, '1:0x') !== false) {
            return 'OK';
        }

        return $ret;
    }

    /**
     * Check if we have to get the session cookie before sending any other http requests to the server
     *
     * @return bool
     */
    private function loginNecessary()
    {
        $cookie = storage_path(self::relCookiePath);

        if (file_exists($cookie) && (time() - filemtime($cookie) >= 30*60)) {
            return true;
        }

        return false;
    }

    public function login()
    {
        $hfcBaseConf = \Modules\HfcBase\Entities\HfcBase::first();

        $data = [
            'username' => $hfcBaseConf->rkm_server_username,
            'password' => $hfcBaseConf->rkm_server_password,
        ];

        $curlOptions = [
            CURLOPT_URL => $hfcBaseConf->rkm_server.'/index.php',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            // Use internal memory (RAM) to store cookie
            // CURLOPT_COOKIEFILE => '',
            CURLOPT_COOKIEJAR => storage_path(self::relCookiePath),
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $ret = curl_exec($ch);
        curl_close($ch);

        Log::debug('Logged in to Sat-Kabel-RKM-server');

        return $ret;
    }

    public function switchState()
    {
        $id = Request::get('id');
        $state = Request::get('state');

        $hfcBaseConf = \Modules\HfcBase\Entities\HfcBase::first();
        $rkmServer = $hfcBaseConf->rkm_server;
        $netelement = NetElement::find($id);

        $tap = explode('~', $netelement->address1);
        $tap = $tap[1] ?? 0;

        $url = $rkmServer.'/index.php?page=rks';
        // $url = $rkmServer.'/index.php';
        $data = [
            // 'action' => 'switchRks' for GET, 'action' => 'switchExt' for POST directly - but doesn't work yet - (returns 'address missing')
            'action' => 'switch',
            'address' => $netelement->address1,
            'type' => 'RKS',
            'tap' => $tap,
            'state' => $state,
            // 'user' => 'user',
            // 'pass' => 'password',
        ];

        // ProvVoipEnviaController for better explanation of options
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            // CURLOPT_COOKIELIST => curl_getinfo($ch, CURLINFO_COOKIELIST)[0],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE => storage_path(self::relCookiePath),
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        $ret = curl_exec($ch);

// d($data, $ch, $ret, curl_getinfo($ch, CURLINFO_HTTP_CODE), curl_error($ch));
        // #RKS+SET: OK (000004D2;0;A;0;000000)
        if (strpos($ret, '#RKS+SET: OK') !== false) {
            $netelement->state = $state;
            $netelement->save();

            return 'OK';
        } elseif ($ret) {
            return $ret;
        }

        $error = curl_error($ch);

        if ($error) {
            return $error;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return 'HTTP Code: '.$httpCode;
    }
}


// Tested but not necessary curl options
// CURLOPT_HTTPHEADER => [
//     'Content-type: application/x-www-form-urlencoded;charset="utf-8"',
//     // 'Accept: text/xml',
//     'Accept: application/json, text/javascript, */*; q=0.01',
//     'Cache-Control: no-cache',
//     'Pragma: no-cache',
// ],
// CURLOPT_USERPWD => "admin:satkabel",
// CURLOPT_COOKIEFILE => storage_path(self::relCookiePath),
// CURLOPT_COOKIEJAR => storage_path(self::relCookiePath),
// CURLOPT_POSTFIELDS => http_build_query($data),
// CURLOPT_COOKIESESSION => true,
// CURLOPT_FOLLOWLOCATION => true,
// CURLOPT_VERBOSE => false,
// return server answer instead of echoing it instantly
// CURLOPT_COOKIE => 'PHPSESSID=e1pugprv6sl7e1u4j9pkobdt54; cod=15.18',
