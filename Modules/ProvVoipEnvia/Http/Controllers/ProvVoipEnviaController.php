<?php namespace Modules\Provvoipenvia\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class ProvVoipEnviaController extends \BaseModuleController {

	public function index()
	{
		return View::make('provvoipenvia::index');
	}

	/**
	 * Performs https request against envial using given URL and payload (XML)
	 *
	 * @author Patrick Reichel
	 *
	 * @param $url URL to use
	 * @param $payload string containing XML to send
	 * @return array containing informations about errors, the http status and the received data
	 */
	protected function _ask_envia($url, $payload) {

		$curl_options = $this->_get_cur_headers($url, $payload);

		// create a new cURL resource
		$ch = curl_init();

		// setting the cURL options
		curl_setopt_array($ch, $curl_options);

		// default values for data array
		$data = array(
			'error' => FALSE,
			'error_type' => null,
			'error_msg' => null,
			'status' => null,
			'xml' => null,
		);

		try {

			// perform cURL session
			$ret = curl_exec($ch);

			// check for errors
			if (curl_errno($ch)) {
				$data['error'] = TRUE;
				$data['error_type'] = "cURL error";
				$data['error_msg'] = curl_error($ch);
			}
			// or get data
			else {
				$data['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$data['xml'] = $ret;
			}
		}
		catch (Exception $ex) {
			$data['error'] = TRUE;
			$data['error_type'] = 'Exception';
			$data['error_msg'] = $ex->getMessage();
		}

		// free the resource
		curl_close($ch);

		return $data;

	}

	/**
	 * Helper to generate the cURL options to use
	 *
	 * @author Patrick Reichel
	 *
	 * @param $url URL to visit
	 * @param $payload data to send
	 *
	 * @return array with cURL options to be set before the request
	 */
	protected function _get_cur_headers($url, $payload) {

		// headers for http request
		$http_headers = array(
			"Content-type: text/xml;charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
		);

		// defining cURL options (http://php.net/manual/en/function.curl-setopt.php)
		$curl_options = array(

			// basic options
			CURLOPT_URL => $url,
			CURLOPT_HTTPHEADER => $http_headers,

			// method and data to use
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => $payload,

			// verify peer's certificate to prevent MITM attacks
			CURLOPT_SSL_VERIFYPEER => TRUE,
			// check for common name in cert and match to the hostname provided
			CURLOPT_SSL_VERIFYHOST => 2,

			// verbose mode?
			CURLOPT_VERBOSE => FALSE,

			// return server answer instead of echoing it instantly
			CURLOPT_RETURNTRANSFER => TRUE,
		);

		return $curl_options;

	}

	public function ping() {

		$url = 'https://www.enviatel.de/portal/api/rest/v1/misc/ping';

		$payload = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<misc_ping>
  <reseller_identifier>
	<username>test_reseller</username>
	<password>test_password</password>
  </reseller_identifier>
</misc_ping>
EOT;

		$data = $this->_ask_envia($url, $payload);

		// major problem!!
		if ($data['error']) {
			echo "ERROR! We got an ".$data['error_type'].": ".$data['error_msg'];
		}
		// got an answer
		else {

			// http status other than 200 OK => something went wrong
			if ($data['status'] != 200) {
				echo "Problem: status code is ".$data['status']."<br>";
			}
			// success!!
			else {
				echo "Success!!";
			}

			echo "Return data:<br>";
			echo "<pre>";
			echo htmlentities($data['xml']);
			echo "</pre>";
		}

	}
}
