<?php

namespace Modules\ProvVoipEnvia\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class ProvVoipEnvia extends \BaseModel {

	/**
	 * Generate the XML used for communication against Envia API
	 *
	 * @author Patrick Reichel
	 *
	 * @param $job job to do
	 * @data $data for which model, e.g. the xml should be build?
	 *
	 * @return XML
	 */
	public function get_xml($job) {

		$this->_create_base_xml_by_topic($job);
		$this->_create_final_xml_by_topic($job);

		return $this->xml->asXML();
	}


	/**
	 * Used to extract error messages from returned XML.
	 *
	 * @author Patrick Reichel
	 *
	 * @param $xml XML to extract error information from
	 */
	public function get_error_messages($xml) {

		$data = array();

		$xml = new \SimpleXMLElement($xml);

		foreach ($xml->response_error as $response_error) {
			$error = array(
				'status' => (string) $response_error->status,
				'message' => (string) $response_error->message,
			);
			array_push($data, $error);
			foreach ($response_error->nested_errors as $nested_error) {
				$error = array(
					'status' => (string) $nested_error->status,
					'message' => (string) $nested_error->message
				);
				array_push($data, $error);
			}
		}

		return $data;
	}


	/**
	 * Create a xml object containing only the top level element
	 *
	 * @param $job job to create xml for
	 */
	protected function _create_base_xml_by_topic($job) {

		// to create simplexml object we first need a string containing valid xml
		// also the prolog should be given; otherwise SimpleXML will not put the
		// attribute “encoding” in…
		$xml_prolog = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml_root = '<'.$job.' />';
		$initial_xml = $xml_prolog.$xml_root;

		// this is the basic xml object which will be extended by other methods
		$this->xml = new \SimpleXMLElement($initial_xml);

	}

	/**
	 * Build the xml extending the basic version.
	 * This will call a method for each second level node, depending on the
	 * given topic. The behavior is controlled by the array $second_level_nodes
	 * which is the mapping between the topic and the xml to create
	 *
	 * @author Patrick Reichel
	 *
	 * @param $job job to do
	 */
	protected function _create_final_xml_by_topic($job) {

		// these elements are used to group the information
		// e.g. in reseller_identifier man will put username and password for
		// authentication against the API
		$second_level_nodes = array(
			'misc_ping' => array(
				'reseller_identifier',
			),
			'misc_get_free_numbers' => array(
				'reseller_identifier',
				'filter_data',
			),
		);

		// now call the specific method for each second level element
		foreach ($second_level_nodes[$job] as $node) {
			$method_name = "_add_".$node;
			$this->${"method_name"}();
		}
	}

	/**
	 * Adds the login data of the reseller to the xml
	 *
	 * @author Patrick Reichecl
	 */
	protected function _add_reseller_identifier() {

		// TODO: add error handling for not existing keys
		// after defining a project wide policy for this kind of problems
		$username = $_ENV['provVoipEnviaResellerUsernameFromDotenv'];
		$password = $_ENV['provVoipEnviaResellerPasswordFromDotenv'];

		$inner_xml = $this->xml->addChild('reseller_identifier');
		$inner_xml->addChild('username', $username);
		$inner_xml->addChild('password', $password);
	}


	/**
	 * Method to add filter data.
	 *
	 * @author Patrick Reichel
	 *
	 */
	protected function _add_filter_data() {

		$localareacode = \Input::get('localareacode', null);
		$baseno = \Input::get('baseno', null);

		// no filters: do nothing
		if (is_null($localareacode)) {
			return;
		}

		// TODO: error handling
		if (!is_numeric($localareacode)) {
			throw \Exception("localareacode has to be numeric");
		}

		// localareacode is valid: add filter
		$inner_xml = $this->xml->addChild('filter_data');
		$inner_xml->addChild('localareacode', $localareacode);

		if (is_null($baseno)) {
			return;
		}

		// TODO: error handling
		if (!is_numeric($baseno)) {
			throw \Exception("baseno has to be numeric");
		}

		// baseno is valid
		$inner_xml->addChild('baseno', $baseno);

	}

}
