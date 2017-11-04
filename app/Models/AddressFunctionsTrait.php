<?php

namespace App\Models;

trait AddressFunctionsTrait {


	/**
	 * Helper to define possible salutation values.
	 * E.g. Envia-API has a well defined set of valid values – using this method we can handle this.
	 *
	 * @author Patrick Reichel
	 */
	public function get_salutation_options() {

		$defaults = [
			'',
			'Herr',
			'Frau',
			'Firma',
			'Behörde',
		];

		if (\PPModule::is_active('provvoipenvia')) {

			// Envia expects Herrn instead of Herr ⇒ to be as compatible as possible to other use cases
			// we nevertheless store Herr in database and fix this in XML generation within
			// ProvVoipEnvia->_add_fields
			$options = [
				'',
				'Herr',
				'Frau',
				'Firma',
				'Behörde',
			];
		}
		else {
			$options = $defaults;
		}

		$result = array();
		foreach ($options as $option) {
			$result[$option] = $option;
		}

		return $result;
	}


	/**
	 * Helper to define possible academic degree values.
	 * E.g. Envia-API has a well defined set of valid values – using this method we can handle this.
	 *
	 * @author Patrick Reichel
	 */
	public function get_academic_degree_options() {

		$defaults = [
			'',
			'Dr.',
			'Prof. Dr.',
		];

		if (\PPModule::is_active('provvoipenvia')) {

			$options = [
				'',
				'Dr.',
				'Prof. Dr.',
			];
		}
		else {
			$options = $defaults;
		}

		$result = array();
		foreach ($options as $option) {
			$result[$option] = $option;
		}

		return $result;
	}


}
