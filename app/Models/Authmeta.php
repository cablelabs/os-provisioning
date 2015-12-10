<?php

class Authmeta extends BaseModel {

	public function users() {
		return $this->belongsToMany('Authuser', 'authusermeta');
	}
}
