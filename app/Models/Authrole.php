<?php

namespace App;

use App\Http\Requests\Request;
use Box\Spout\Common\Exception\EncodingConversionException;
use DB;
//use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Authrole extends BaseModel
{
    public $table = 'authmetas';

	public static function view_headline()
	{
		return 'User roles';
	}

	public static function rules($id=null)
	{
		return array(
			'name' => 'required',
			'type' => 'required|in:role,client'
		);
	}

	// link title in index view
	public function view_index_label()
	{
		return [
			'index' => [$this->id, $this->name, $this->type],
			'index_header' => ['', 'Role name', 'Type'],
			'header' => $this->name
		];
	}

	public function view_has_many()
	{
		$ret['Base']['Rights']['view']['view'] = 'auth.rights';
		return $ret;
	}

	/**
	 * Returns all available roles
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_roles()
	{
		$ret = array();

		try {
			$ret = DB::table($this->table)
				->select('id', 'name')
				->where('type', 'LIKE', '%role%')
				->get();
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}
		return $ret;
	}

	/**
	 * Returns all user assigned roles
	 *
	 * @param integer $user_id
	 * @return array
	 * @throws \Exception
	 */
	public function get_roles_by_userid($user_id)
	{
		$ret = array();

		try {
			$ret = DB::table($this->table)
				->join('authusermeta', 'authusermeta.meta_id', '=', $this->table . '.id')
				->select($this->table . '.id', $this->table . '.name')
				->where($this->table . '.type', 'LIKE', '%role%')
				->where('authusermeta.user_id', '=', (int) $user_id)
				->get();
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}
		return $ret;
	}

	/**
	 * Returns roles which aren't assigned to given user
	 *
	 * @param integer $user_id
	 * @return array
	 * @throws \Exception
	 */
	public function get_not_assigned_roles_by_userid($user_id)
	{
		$ret = array();

		try {
			$all_roles = $this->get_roles();
			$user_assigned_roles = $this->get_roles_by_userid($user_id);

			foreach ($all_roles as $key => $role) {

				if (!$this->_check_assigned_role($user_assigned_roles, $role)) {
					$ret[] = $role;
				}
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}

		return $ret;
	}

	/**
	 * Check if role assigned to user
	 *
	 * @param array $user_assigned_roles
	 * @param object $role
	 * @return bool
	 * @throws \Exception
	 */
	private function _check_assigned_role($user_assigned_roles, $role)
	{
		$role_is_assigned = false;

		try {
			foreach ($user_assigned_roles as $key => $user_role) {
				if ($user_role->id == $role->id) {
					$role_is_assigned = true;
				}
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode(), $e);
		}

		return $role_is_assigned;
	}
}
