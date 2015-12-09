<?php namespace Modules\Provvoipenvia\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

use Modules\ProvVoipEnvia\Entities\ProvVoipEnvia;

class ProvVoipEnviaController extends \BaseModuleController {

	/**
	 * Constructor.
	 *
	 * @author Patrick Reichel
	 */
	public function __construct() {

		$this->model = new ProvVoipEnvia();

	}


	/**
	 * Overwrite index.
	 */
	public function index() {
		$base = "/lara/provvoipenvia/request";

		$jobs = array(
			'misc_ping',
			'misc_get_free_numbers',
			'misc_get_free_numbers?localareacode=03725',
			'misc_get_free_numbers?localareacode=03725&amp;baseno=110',
		);

		foreach ($jobs as $job) {
			echo '<a href="'.$base.'/'.$job.'" target="_self">'.$job.'</a><br>';
		}
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

		$curl_options = $this->_get_curl_headers($url, $payload);

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
	protected function _get_curl_headers($url, $payload) {

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

	/**
	 * Helper to show the generated XML (in original and pretty shape)
	 * Use this for debugging the XML output and input
	 *
	 * @author Patrick Reichel
	 */
	private function __debug_xml($xml) {

		echo "<pre style=\"border: solid 1px #444; padding: 10px\">";
		echo "<h5>Pretty:</h5>";
		$dom = new \DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml);
		echo htmlentities($dom->saveXML());
		echo "<br><hr>";
		echo "<h5>Original:</h5>";
		echo htmlentities($xml);
		echo "</pre>";
	}

	/**
	 * Method to perform a request the envia API.
	 *
	 * @author Patrick Reichel
	 *
	 * @param $job comes from the route ([…]/provvoipenvia/request/{job})
	 */
	public function request($job) {

		// the URLs to use for the jobs to do
		$urls = array(
			'misc_ping' => 'https://www.enviatel.de/portal/api/rest/v1/misc/ping',
			'misc_get_free_numbers' => 'https://www.enviatel.de/portal/api/rest/v1/misc/get_free_numbers',
		);

		// TODO: improve error handling
		if (!array_key_exists($job, $urls)) {
			throw new \Exception("Job ".$job." not implemented yet");
		}

		// the API URL to use for the request
		$url = $urls[$job];

		// the requests payload (=XML)
		$payload = $this->model->get_xml($job);

		$this->__debug_xml($payload);

		// perform the request and receive the result (meta and content)
		$data = $this->_ask_envia($url, $payload);

		// major problem!!
		if ($data['error']) {
			$this->_handle_curl_error($job, $data);
		}
		// got an answer
		else {
			$this->_handle_curl_success($job, $data);
		}

	}

	/**
	 * Method to handle exceptions and curl errors
	 *
	 * @author Patrick Reichel
	 *
	 * @param $job job which should have been done
	 * @param $data collected data from request try
	 */
	protected function _handle_curl_error($job, $data) {
		echo "ERROR! We got an ".$data['error_type'].": ".$data['error_msg']." executing job ".$job;
	}

	/**
	 * Method to handle successful request (on cURL level).
	 * Mainly used to separate further process using the HTTP status code.
	 *
	 * @author Patrick Reichel
	 *
	 * @param $job job which should have been done
	 * @param $data collected data from request try
	 */
	protected function _handle_curl_success($job, $data) {

		// success!!
		if (($data['status'] >= 200) && ($data < 300)) {
			$this->_handle_request_success($job, $data);
		}
		// unauthorized => handle separately
		elseif ($data['status'] == 401) {
			$this->_handle_request_failed_401($job, $data);
		}
		// other => something went wrong
		else {
			$this->_handle_request_failed($job, $data);
		}

		echo "<hr>";
		echo "Return data:<br>";
		echo "<pre>";
		echo htmlentities($data['xml']);
		echo "</pre>";
	}

	/**
	 * Process rest answers with http error status 401 (Access denied)
	 *
	 * @author Patrick Reichel
	 *
	 * @param $job job which should have been done
	 * @param $data collected data from request try
	 */
	protected function _handle_request_failed_401($job, $data) {

		$errors = $this->model->get_error_messages($data['xml']);

		// TODO: Error output shall be handled via views
		echo "The following errors occured:<br>";
		echo "<table style=\"background-color: #faa\">";
		foreach ($errors as $error) {
			echo "<tr>";
			echo "<td>";
				echo $error['status'];
			echo "</td>";
			echo "<td>";
				echo $error['message'];
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	/**
	 * Process rest answers with http error status (400, 401, e.g.)
	 *
	 * @author Patrick Reichel
	 *
	 * @param $job job which should have been done
	 * @param $data collected data from request try
	 */
	protected function _handle_request_failed($job, $data) {

		echo "Problem: status code is ".$data['status']."<br>";
	}

	/**
	 * Process successfully performed REST request.
	 *
	 * @author Patrick Reichel
	 *
	 * @param $job job which should have been done
	 * @param $data collected data from request try
	 */
	protected function _handle_request_success($job, $data) {
	}

}
