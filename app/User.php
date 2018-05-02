<?php

namespace App;

use DB;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * This is the Model, holding the User data for authentication.
 * A User belongsToMany Roles and a Role role holds CRUD
 * separated Permissions. To gain access data the
 * Middleware will check for Permissions.
 */
class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
	use Authenticatable, Authorizable, Notifiable;

	public $table = 'users';

	/**
	 * BOOT:
	 * - init observer
	 */
	public static function boot()
	{
		parent::boot();

		User::observe(new UserObserver);
	}

	/**
	 * Database related Code
	 *
	 * @author Patrick Reichel & Christian Schramm
	 */
	public function roles()
	{
			return $this->belongsToMany(Role::class);
	}

	public function tickets() {
		return $this->belongsToMany('\Modules\Ticketsystem\Entities\Ticket', 'ticket_user', 'user_id', 'ticket_id');
	}

	/**
	 * Assign the given role to the user.
	 *
	 * @param  string $role
	 * @return mixed
	 */
	public function assignRole($role)
	{
		return $this->roles()->save(
			Role::whereName($role)->firstOrFail()
		);
	}

	/**
	 * Determine if the user has the given role.
	 *
	 * @param  mixed $role
	 * @return boolean
	 */
	public function hasRole($role)
	{
			if (is_string($role)) {
					return $this->roles->contains('name', $role);
			}

			return !! $role->intersect($this->roles)->count();
	}

	/**
	 * Determine if the user may perform the given permission.
	 *
	 * @param  Permission $permission
	 * @return boolean
	 */
	public function hasPermission(Permission $permission)
	{
			return $this->hasRole($permission->roles);
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

		return $this->roles->contains('name', $superuserRoleName);
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

/*
 * Observer Class
 */
class UserObserver
{
    public function created($user)
    {
			$id = $user->id;

			// Create required AuthUser_role relation, otherwise user can not login
			// 2017-03016 SAr: Assign relation only for the root user
			if ($id == 1) {
				DB::update("INSERT INTO role_user (user_id, role_id) VALUES($id, 1);");
				DB::update("INSERT INTO role_user (user_id, role_id) VALUES($id, 2);");
			}
    }
}
