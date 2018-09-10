<?php

namespace App;

use Bouncer;
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

    protected $guard = 'admin';

    /**
     * extending the boot functionality to observe changes
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::observe(new UserObserver);
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

    public function tickets()
    {
        return $this->belongsToMany('\Modules\Ticketsystem\Entities\Ticket', 'ticket_user', 'user_id', 'ticket_id');
    }

    /**
     * Validation
     *
     *  Add your validation rules here
     */
    public static function rules($id = null)
    {
        return [
            'login_name' => 'required|unique:users,login_name,'.$id.',id,deleted_at,NULL',
            'password' => 'sometimes|min:10|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|confirmed',
            'password_confirmation' => 'min:10|required_with:password|same:password',
        ];
    }

    /**
     * View related Code
     */

    /**
     * Name which is Displayed on Top and in Headline
     */
    public static function view_headline() : string
    {
        return 'Users';
    }

    /**
     *  Icon for this model
     */
    public static function view_icon() : string
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

    public function getHighestRank() : int
    {
        $ranks = $this->roles()->pluck('rank');
        $highestRank = 0;

        foreach ($ranks as $rank) {
            $highestRank = $rank > $highestRank ? $rank : $highestRank;
        }

        return $highestRank;
    }

    public static function getHighestRankOf(self $user) : int
    {
        $ranks = $user->roles()->pluck('rank');
        $highestRank = 0;
        foreach ($ranks as $rank) {
            $highestRank = $rank > $highestRank ? $rank : $highestRank;
        }

        return $highestRank;
    }

    public function hasHigherRankThan(self $user) : bool
    {
        return $this->getHighestRank() > $user->getHighestRank() ? true : false;
    }

    public function hasLowerRankThan(self $user) : bool
    {
        return $this->getHighestRank() < $user->getHighestRank() ? true : false;
    }

    public function hasSameRankAs(self $user) : bool
    {
        return $this->getHighestRank() == $user->getHighestRank() ? true : false;
    }
}

class UserObserver
{
    public function created($user)
    {
        Bouncer::allow($user)->toOwn(User::class);
    }

    public function updating($user)
    {
        // Rebuild cached sidebar when user changes his language
        if ($user['original']['language'] != $user['attributes']['language']) {
            \Session::forget('menu');
        }
    }
}
