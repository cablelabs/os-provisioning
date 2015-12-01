<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

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
class Authuser extends BaseModel implements UserInterface, RemindableInterface {
/* class Authuser extends BaseModel implements RemindableInterface { */

	use UserTrait;
	use RemindableTrait;

	/**
	 * Get all the meta entities (roles, clients) the user belongs to
	 */
	protected function _meta() {
		return $this->belongsToMany('Authmeta', 'authusermeta', 'user_id', 'meta_id');
	}

	/**
	 * Get all the users roles.
	 * Roles are meta entities mapping users to models
	 */
	public function roles() {
		return $this->_meta()->where('type', 'LIKE', 'role')->get();
		/* return $this->_meta()->where('type', 'is', 'role')->get(); */
	}

	/**
	 * Get all model information related to a given roll
	 */
	protected function _role_models($role_id) {
		return Authmeta::cores_by_meta($role_id, 'model');
	}

	/**
	 * Get all clients the users belongs to.
	 * Clients are meta entities mapping users to nets
	 */
	public function clients() {
		return $this->_meta()->where('type', 'LIKE', 'client');
	}


	/**
	 * Get a matrix containing user rights for models.
	 *
	 * @return two dimensional array [modelname][rights]
	 */
	public function models() {

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
				// as a user can hold many roles there can be different permissions for a task
				foreach ($perm_types as $perm_type) {
					$permissions[$name][$perm_type] = max($permissions[$name][$perm_type], $model->{$perm_type});
				}

			}

			echo "<pre>";
			print_r($permissions);
			echo "</pre>";
		}
		exit(1);
	}

	/**
	 * Get a matrix containing user rights for nets.
	 *
	 * @return two dimensional array [net][rights]
	 */
	public function nets() {
		echo "TODO";
	}


	/**
	 * Check if user is allowed to access a model the given way
	 *
	 * @param $model name of the model
	 * @param $access_type right needed (create, edit, delete, view)
	 *
	 * @return True if asked access is allowed, else false
	 */
	public function hasModel($model, $access) {


		$this->models();
		return $model." !!";

		$usermodels = $this->models();

		if (!array_key_exists($usermodels)) {
			return False;
		}

		if ($usermodels[$model] == 0) {
			return False;
		}

		return True;
	}

	/**
	 * Create a user and add meta entities
	 *
	 * @var $meta array with meta entities a user should get
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
}
