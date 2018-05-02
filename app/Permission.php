<?php

namespace App;

/**
 *
 *	a) Has the user the right to read/write the underlying model?
 *	b) Is the queried entity (e.g. modem) in a network the user is allowed to read from/write in?
 *	c) If one wants to create/edit/delete an entity model AND net must allow writing!
 */

class Permission extends BaseModel {


	public function roles() {
		return $this->belongsToMany(Role::class);
	}

	/*
	  Update models in database table
		public function updateModels() {
		print_r($this->get_models());
		}
	*/

}
