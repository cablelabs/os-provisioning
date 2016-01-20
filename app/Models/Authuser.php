<?php

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
			'login_name' => 'required|unique:authusers,login_name,'.$id,
			'password' => 'required|min:6'
		);
	}

	// Don't forget to fill this array
	protected $fillable = ['first_name', 'last_name', 'email', 'login_name', 'password', 'active', 'description'];


	// Name of View
	public static function get_view_header()
	{
		return 'Users';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return $this->login_name;
	}



	/**
	 * Get all the meta entities (roles, clients) the user belongs to
	 *
	 * @author Patrick Reichel
	 */
	protected function _meta() {
		return $this->belongsToMany('Authmeta', 'authusermeta', 'user_id', 'meta_id');
	}

	/**
	 * Get all the users roles.
	 * Roles are meta entities mapping users to models
	 *
	 * @author Patrick Reichel
	 */
	public function roles() {
		return $this->_meta()->where('type', 'LIKE', 'role')->get();
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
		$perm_types = array('view', 'create', 'edit', 'delete', 'ext_provider_actions');

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
	 * @param $access_type right needed (create, edit, delete, view, ext_provider_actions)
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

		Authuser::observe(new \AuthObserver);
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
		DB::update("INSERT INTO authusermeta (user_id, meta_id) VALUES($id, 1);");
		DB::update("INSERT INTO authusermeta (user_id, meta_id) VALUES($id, 2);");
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
