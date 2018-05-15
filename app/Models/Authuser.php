<?php

namespace App;

use DB;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * Model holding user data for authentication
 *
 * A user belongs to roles and clients.
 * A role holds several models, a client several nets. There is a separation for read and write rights.
 * To gain access data we later have to check:
 *	a) Has the user the right to read/write the underlying model?
 *	b) Is the queried entity (e.g. modem) in a network the user is allowed to read from/write in?
 *	c) If man wants to create/edit/delete an entity model AND net must allow writing!
 */
class Authuser extends BaseModel implements AuthenticatableContract, CanResetPasswordContract {
/* class Authuser extends BaseModel implements RemindableInterface { */

	use Authenticatable, CanResetPassword;


	// The associated SQL table for this Model
	public $table = 'authusers';

	public $guarded = ['roles_ids'];

	// Add your validation rules here
	public static function rules($id=null)
	{
		return array(
			'login_name' => 'required|unique:authusers,login_name,'.$id.',id,deleted_at,NULL',
			'password' => 'required|min:6'
		);
	}


	// Name of View
	public static function view_headline()
	{
		return 'Users';
	}

	// View Icon
	public static function view_icon()
	{
		return '<i class="fa fa-user-o"></i>';
	}

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label()
	{
		// TODO: set color dependent of user permissions
		//$bsclass = $this->get_bsclass();

		return ['table' => $this->table,
				'index_header' => [$this->table.'.login_name', $this->table.'.first_name', $this->table.'.last_name'],
				'header' => $this->first_name.' '.$this->last_name,
			];
	}


	/**
	 * Get all the meta entities (roles, clients) the user belongs to
	 *
	 * @author Patrick Reichel
	 */
	protected function _meta() {
		return $this->belongsToMany('App\Authrole', 'authuser_role', 'user_id', 'role_id');
	}


	/**
	 * Get all the users roles.
	 * Roles are meta entities mapping users to models
	 *
	 * @author Patrick Reichel
	 */
	public function roles() {
		return $this->_meta()->where('type', 'LIKE', 'role')->orderBy('id');
		// return $this->_meta()->where('type', 'LIKE', 'role')->orderBy('id')->get();
	}


	/**
	 * Get all model information related to a given roll
	 *
	 * @author Patrick Reichel
	 */
	protected function _role_models($role_id) {
		return Authrole::cores_by_role($role_id, 'model');
	}

	/**
	 * Get all clients the users belongs to.
	 * Clients are meta entities mapping users to nets
	 *
	 * @author Patrick Reichel
	 */
	public function clients() {
		return $this->_meta()->where('type', 'LIKE', 'client');
	}

	public function tickets() {
		return $this->belongsToMany('\Modules\Ticketsystem\Entities\Ticket', 'ticket_user', 'user_id', 'ticket_id');
	}

	/**
	 * Get a matrix containing user rights for models.
	 *
	 * @author Patrick Reichel
	 *
	 * @return two dimensional array [modelname][rights]
	 */
	public function get_model_permissions() {

		$permissions = array();
		$perm_types = array('view', 'create', 'edit', 'delete');

		// get data for each role a user has
		foreach ($this->roles as $role) {
			// get all models for the current role
			$models = $this->_role_models($role['id']);
			// get permissions per model
			foreach ($models as $model) {

				$name = $model->name;

				// create entry without permissions if model not exists
				if (!array_key_exists($name, $permissions)) {
					$perm = array();
					foreach($perm_types as $perm_type) {
						$perm[$perm_type] = 0;
					}
					$permissions[$name] = $perm;
				}

				// use highest rights for the model
				// as a user can hold many roles there can be different permissions for a task ⇒ if one role allows access, than access is granted
				foreach ($perm_types as $perm_type) {
					$permissions[$name][$perm_type] = max($permissions[$name][$perm_type], $model->{$perm_type});
				}

			}

		}

		return $permissions;
	}

	/**
	 * Get a matrix containing user rights for nets.
	 *
	 * @author Patrick Reichel
	 *
	 * @return two dimensional array [net][rights]
	 */
	public function nets() {
		echo "TODO";
	}


	/**
	 * Check if user is allowed to access a net the given way
	 *
	 * @author Patrick Reichel
	 *
	 * @param $model name of the net
	 * @param $access_type right needed (create, edit, delete, view)
	 *
	 * @return True if asked access is allowed, else false
	 */
	public function has_net($net, $access) {

		// TODO
		log.warning('Method “hasNet“ in model Authuser is not yet implemented (returns always true!');
		return True;
	}



	/**
	 * BOOT:
	 * - init observer
	 */
	public static function boot()
	{
		parent::boot();

		Authuser::observe(new AuthuserObserver);
	}



	/**
	 * Check if user has super_admin rights
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function is_admin()
	{
		$super_user_role_id = 1;

		return $this->roles->contains('id', $super_user_role_id);
	}

	/**
	 * Check if user has permissions for module and model
	 *
	 * @param $module
	 * @param $entity
	 * @return bool
	 * @throws \Exception
	 */
	public function has_permissions($module, $entity)
	{
		$ret_val = false;
		$namespace = 'Modules\\' . $module . '\\Entities\\' . $entity;
		if ($module == 'App\\') {
			// separately added page
			if ($entity == 'Config')
				$entity = 'GlobalConfig';
			$namespace = $module . $entity;
		}

		$model_permissions = $this->get_model_permissions();

		if (array_key_exists($namespace, $model_permissions)) {
			$ret_val = true;
		}

		return $ret_val;
	}
}


/*
 * Observer Class
 */
class AuthuserObserver
{
	public function created($user)
	{
		$id = $user->id;

		// Create required AuthUser_role relation, otherwise user can not login
		// 2017-03016 SAr: Assign relation only for the root user
		if ($id == 1) {
			DB::update("INSERT INTO authuser_role (user_id, role_id) VALUES($id, 1);");
			DB::update("INSERT INTO authuser_role (user_id, role_id) VALUES($id, 2);");
		}
	}

	public function deleted($user)
	{
		// Drop AuthUser_role Relation
		DB::table('authuser_role')->where('user_id', '=', $user->id)->delete();
	}

}
