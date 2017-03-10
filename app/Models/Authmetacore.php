<?php

namespace App;

/*
 * This is a simple placeholder, so that we can use Authmetacore from Eloquent context
 * NOTE: This shit :) is actually used by Command: php artisan nms:auth
 */
use Illuminate\Support\Facades\DB;

class Authmetacore extends BaseModel {

	protected $table = 'authmetacore';

	/**
	 * Get all assigned rights to a role
	 *
	 * @param $meta_id
	 * @return array
	 * @throws \Exception
	 */
	public function get_rights_by_metaid($meta_id)
	{
		$ret = array();

		try {
			if (!is_null($meta_id)) {
				$ret = DB::table($this->table)
					->join('authcores', 'authcores.id', '=', $this->table . '.core_id')
					->select($this->table . '.*', 'authcores.name', 'authcores.type')
					->where($this->table . '.meta_id', '=', $meta_id)
					->get();
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}

		return $ret;
	}

	/**
	 * Update view\create\edit\delete permissions for Model
	 *
	 * @param $authmethacore_id
	 * @param $authmethacore_right
	 * @param $authmethacore_right_value
	 * @return mixed
	 * @throws \Exception
	 */
	public function update_permission($authmethacore_id, $authmethacore_right, $authmethacore_right_value)
	{

		try {
			if ($authmethacore_right_value == 1) {
				$value = 0;
			} elseif ($authmethacore_right_value == 0) {
				$value = 1;
			}

			$ret = DB::table($this->table)
				->where('id', '=' , $authmethacore_id)
				->update([$authmethacore_right => $value]);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}
		return $ret;
	}
}
