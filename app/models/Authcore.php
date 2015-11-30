<?php

class Authcore extends BaseModel {

	/**
	 * Update models in database table
	 */
	public updateModels() {

		print_r($this->_get_models());
	}
}
