<?php

namespace App;

use DB;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * This is the Model, holding the User data for authentication.
 * A User belongsToMany Roles and a Role role holds CRUD
 * separated Permissions. To gain access data the
 * Middleware will check for Permissions.
 */
class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
	use Authenticatable, Authorizable, HasRolesAndAbilities, Notifiable;

	public $table = 'users';

	public function tickets() {
		return $this->belongsToMany('\Modules\Ticketsystem\Entities\Ticket', 'ticket_user', 'user_id', 'ticket_id');
	}

	/**
	 * Determine if the user has the given role.
	 *
	 * @param  mixed $role
	 * @return boolean
	 */
	public function hasRole($role)
	{
			return ($this)->is($role);
	}

	/**
	 * Check if user has super_admin rights
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function isSuperAdmin()
	{
		$superuserRoleName = 'super_admin';

		return $this->is($superuserRoleName);
	}

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'login_name',
		'email',
		'password',
		'language',
		'active',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Validation
	 *
	 *  Add your validation rules here
	 */
	public static function rules($id=null)
	{
		return array(
			'login_name' => 'required|unique:users,login_name,'.$id.',id,deleted_at,NULL',
			'password' => 'required|min:6'
		);
	}

	/**
	 * View related Code
	 */
	/**
	 * Name which is Displayed on Top and in Headline
	 */
	public static function view_headline()
	{
		return 'Users';
	}

	/**
	 *  Icon for this model
	 */
	public static function view_icon()
	{
		return '<i class="fa fa-user-o"></i>';
	}

	/**
	 * This Method returns a configuration array to generate
	 * the datatables on the index Page of each module.
	 *
	 * For more documentation look in BaseController
	 * TODO: set color dependent of user role/permission
	 */
	public function view_index_label()
	{

		return ['table' => $this->table,
				'index_header' => [$this->table.'.login_name', $this->table.'.first_name', $this->table.'.last_name'],
				'header' => $this->first_name.' '.$this->last_name,
			];
	}
}
