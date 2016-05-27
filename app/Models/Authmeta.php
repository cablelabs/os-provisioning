<?php

namespace App;

class Authmeta extends BaseModel {

	public function users() {
		return $this->belongsToMany('Authuser', 'authusermeta');
	}

	public function cores() {
		return $this->belongsToMany('Authcore', 'authmetacore');
	}

	/**
	 * returns all cores matching the given meta ID and core type
	 *
	 * @param $meta_id ID of the meta entry to fetch data for
	 * @param $core_type [model|net]
	 *
	 * @return database data
	 */
	public static function cores_by_meta($meta_id, $core_type) {
		return \DB::table('authmetacore')
					->join('authcores', 'authmetacore.core_id', '=', 'authcores.id')
					->select('authcores.name', 'authmetacore.view', 'authmetacore.create', 'authmetacore.edit', 'authmetacore.delete')
					->where('authmetacore.meta_id', '=', $meta_id)
					->where('authcores.type', 'LIKE', $core_type)
					->get();
	}

}
