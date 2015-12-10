<?php

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

	/**
	 * Get all the meta entities (roles, clients) the user belongs to
	 */
	protected function _meta() {
		return $this->belongsToMany('AuthMeta', 'authusermeta');
	}

	/**
	 * Get all the users roles.
	 * Roles are meta entities mapping users to models
	 */
	public function roles() {
		return $this->_meta->where('type', 'is', 'role');
	}

	/**
	 * Get all clients the users belongs to.
	 * Clients are meta entities mapping users to nets
	 */
	public function clients() {
		return $this->_meta->where('type', 'is', 'client');
	}

	/**
	 * Get a matrix containing user rights for models.
	 *
	 * @return two dimensional array [modelname][rights]
	 */
	public function models() {
		echo "TODO";
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
