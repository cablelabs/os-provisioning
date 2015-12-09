<?php

namespace Modules\ProvVoipEnvia\Entities;

// Model not found? execute composer dump-autoload in lara root dir
class ProvVoipEnvia extends \BaseModel {

	/**
	 * Generate the XML used for communication against Envia API
	 *
	 * @author Patrick Reichel
	 *
	 * @param $topic job to do
	 * @data $data for which model, e.g. the xml should be build?
	 *
	 * @return XML
	 */
	public function get_xml($topic, $data) {

		$this->_create_base_xml_by_topic($topic);
		$this->_create_final_xml_by_topic($topic);

		return $this->xml->asXML();
	}

	/**
	 * Create a xml object containing only the top level element
	 *
	 * @param $topic job to create xml for
	 */
	protected function _create_base_xml_by_topic($topic) {

		// dict with top element names by topic
		$root_elements = array(
			'ping' => 'misc_ping',
		);

		$root_element = $root_elements[$topic];

		// to create simplexml object we first need a string containing valid xml
		$xml_prolog = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml_root = '<'.$root_element.' />';
		$initial_xml = $xml_prolog.$xml_root;

		// this is the complete xml object
		$this->xml = new \SimpleXMLElement($initial_xml);

	}

	/**
	 * Build the xml extending the basic version
	 *
	 * @author Patrick Reichel
	 */
	protected function _create_final_xml_by_topic($topic) {

		// these elements are used to group the information
		// e.g. in reseller_identifier man will put username and password for
		// authentication against the API
		$second_level_nodes = array(
			'ping' => array(
				'reseller_identifier',
			),
		);

		// now call the specific method for each second level element
		foreach ($second_level_nodes[$topic] as $node) {
			$method_name = "_add_".$node;
			$this->${"method_name"}();
		}
	}

	protected function _add_reseller_identifier() {

		// TODO: add error handling for not existing keys
		// after defining a project wide policy for this kind of problems
		$username = $_ENV['provVoipEnviaResellerUsernameFromDotenv'];
		$password = $_ENV['provVoipEnviaResellerPasswordFromDotenv'];

		$inner_xml = $this->xml->addChild('reseller_identifier');
		$inner_xml->addChild('username', $username);
		$inner_xml->addChild('password', $password);
	}

}
