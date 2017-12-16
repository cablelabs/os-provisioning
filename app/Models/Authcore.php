<?php

namespace App;

class Authcore extends BaseModel {


	public function metas() {
		return $this->belongsToMany('App\Authrole', 'authrole_core');
	}


	/**
	 * Update models in database table
	 */
	public function updateModels() {

		print_r($this->get_models());
	}
}
