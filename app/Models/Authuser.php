<?php

namespace App;

use DB;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

// require_once(app_path().'/Exceptions.php');

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


	// link title in index view
	public function view_index_label()
	{
		// TODO: set color dependent of user permissions
		// 'bsclass' => $bsclass,

		return ['index' => [$this->login_name, $this->first_name, $this->last_name],
		        'index_header' => ['Login', 'Firstname', 'Lastname'],
		        'header' => $this->login_name];
	}



	/**
	 * Get all the meta entities (roles, clients) the user belongs to
	 *
	 * @author Patrick Reichel
	 */
	protected function _meta() {
		return $this->belongsToMany('App\Authmeta', 'authusermeta', 'user_id', 'meta_id');
	}

	/**
	 * Get all the users roles.
	 * Roles are meta entities mapping users to models
	 *
	 * @author Patrick Reichel
	 */
	public function roles() {
		return $this->_meta()->where('type', 'LIKE', 'role')->orderBy('id')->get();
	}

	/**
	 * Get all model information related to a given roll
	 *
	 * @author Patrick Reichel
	 */
	protected function _role_models($role_id) {
		return Authmeta::cores_by_meta($role_id, 'model');
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
		foreach ($this->roles() as $role) {

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
	 * Create a user and add meta entities
	 *
	 * @author Patrick Reichel
	 *
	 * @param $metagroups array with meta entities a user should get
	 */
	public function makeUser($metagroups) {

		// TODO: Check if create right for users is set!

		$groups = array_fetch(Authmeta::all()->toArray(), 'name');
		$usergroups = array();

		// check if given metagroups exist in database
		// apply group to user
		foreach ($metagroups as $metagroup) {
			if (array_search($metagroup, $groups) !== False) {
				array_push($usergroups, $this->getIdInArray($groups, $metagroup));
			}
		}

		$this->_meta()->attach($usergroups);

	}

	/**
	 * BOOT:
	 * - init observer
	 */
	public static function boot()
	{
		parent::boot();

		Authuser::observe(new AuthObserver);
	}

	public function view_has_many()
	{
		$ret['Base']['Roles']['view']['view'] = 'auth.roles';
		$ret['Base']['Roles']['view']['vars']['roles'] = $this->roles();

		return $ret;
	}

	/**
	 * Remove role from user
	 *
	 * @param $user_id
	 * @param $role_id
	 * @return null
	 * @throws \Exception
	 */
	public function delete_roles_by_userid($user_id, $role_id)
	{
		$ret = null;

		try {
			// In the case the user would like to delete the super_admin role, it's
			// necessary to check how many users have the role too. If the user the last one,
			// the role can't be deleted
			if ($role_id == 1) {
				$count = DB::table('authusermeta')
					->where('meta_id', '=', $role_id)
					->count();

				if ($count == 1) {
					\Session::flash('role_error', 'Could not delete role "super_admin"!');
					return $ret;
				}
			}

			$ret = DB::table('authusermeta')
				->where('user_id', '=', $user_id)
				->where('meta_id', '=', $role_id)
				->delete();
		} catch (\Exception $e) {
			throw $e;
		}
		return $ret;
	}

	/**
	 * Assign role to user
	 *
	 * @param $user_id
	 * @param $role_id
	 * @return null
	 * @throws \Exception
	 */
	public function assign_roles_for_userid($user_id, $role_id)
	{
		$ret = null;

		try {
			$ret = DB::table('authusermeta')
				->insert(array('user_id' => $user_id, 'meta_id' => $role_id));
		} catch (\Exception $e) {
			throw $e;
		}
		return $ret;
	}

	/**
	 * Check if user has super_admin rights
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function is_admin()
	{
		$ret_val = false;
		$super_user_role_id = 1;

		try {
			$roles = $this->roles();

			foreach ($roles as $role) {
				if ($role->id == $super_user_role_id) {
					$ret_val = true;
					break;
				}
			}
		} catch (\Exception $e) {
			throw $e;
		}
		return $ret_val;
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
		try {
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
		} catch (\Exception $e) {
			throw $e;
		}
		return $ret_val;
	}
}


/*
 * Observer Class
 */
class AuthObserver
{
    public function created($auth)
    {
		$id = $auth->id;

		// Create required AuthUserMeta relation, otherwise user can not login
		// 2017-03016 SAr: Assign relation only for the root user
		if ($id == 1) {
			DB::update("INSERT INTO authusermeta (user_id, meta_id) VALUES($id, 1);");
			DB::update("INSERT INTO authusermeta (user_id, meta_id) VALUES($id, 2);");
		}
    }

    public function deleted($auth)
    {
		// Drop AuthUserMeta Relation
		DB::table('authusermeta')->where('user_id', '=', $auth->id)->delete();

		// Hard Delete this entry. Because in SQL the login_name is unique
		// a soft deleted login_name entry will cause problems while adding
		// a new entry
		//
		// TODO: use a global define to disable Soft Deletes
		Authuser::onlyTrashed()->forceDelete();
    }
}
