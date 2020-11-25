<?php

namespace App;

use App;
use Bouncer;
use Session;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Modules\Ticketsystem\Entities\Ticket;
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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'users';

    /**
     * The authentication guard name.
     *
     * @var string
     */
    protected $guard = 'admin';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'last_login_at',
        'geopos_updated_at',
        'password_changed_at',
    ];

    /**
     * Expiration duration for the user position data.
     */
    public const GEOPOS_EXPIRATION_TIME = '1 day';

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
        'password_changed_at',
        'initial_dashboard',
        'truck',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_user', 'user_id', 'ticket_id');
    }

    /**
     * Query for the new and open Tickets only.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function openTickets()
    {
        return $this->tickets()->where('state', '!=', 'closed');
    }

    public function inWorkTickets()
    {
        return $this->tickets()->where('state', 'in work');
    }

    /**
     * Validation
     *
     *  Add your validation rules here
     */
    public function rules()
    {
        $id = $this->id;

        return [
            'email' => 'nullable|email',
            'login_name' => 'required|unique:users,login_name,'.$id.',id,deleted_at,NULL',
            'password' => 'sometimes|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
            'password_confirmation' => 'min:8|required_with:password|same:password',
        ];
    }

    /**
     * View related Code
     */

    /**
     * Name which is Displayed on Top and in Headline
     */
    public static function view_headline(): string
    {
        return 'Users';
    }

    /**
     *  Icon for this model
     */
    public static function view_icon(): string
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
            'index_header' => [$this->table.'.login_name', $this->table.'.first_name', $this->table.'.last_name', 'email', $this->table.'.geopos_updated_at', 'active'],
            'header' => $this->first_name.' '.$this->last_name,
        ];
    }

    public function getHighestRank(): int
    {
        $ranks = $this->roles()->pluck('rank');
        $highestRank = 0;

        foreach ($ranks as $rank) {
            $highestRank = $rank > $highestRank ? $rank : $highestRank;
        }

        return $highestRank;
    }

    public static function getHighestRankOf(self $user): int
    {
        $ranks = $user->roles()->pluck('rank');
        $highestRank = 0;
        foreach ($ranks as $rank) {
            $highestRank = $rank > $highestRank ? $rank : $highestRank;
        }

        return $highestRank;
    }

    public function hasHigherRankThan(self $user): bool
    {
        return $this->getHighestRank() > $user->getHighestRank() ? true : false;
    }

    public function hasLowerRankThan(self $user): bool
    {
        return $this->getHighestRank() < $user->getHighestRank() ? true : false;
    }

    public function hasSameRankAs(self $user): bool
    {
        return $this->getHighestRank() == $user->getHighestRank() ? true : false;
    }

    /**
     * Checks if this is the first own Login of a User.
     *
     * @param App\User $user
     * @return bool
     */
    public function isFirstLogin(): bool
    {
        return $this->last_login_at == null || $this->password_changed_at == null;
    }

    /**
     * Checks if the password of the current user is expired.
     *
     * @param App\User $user
     * @param Carbon\Carbon $now
     * @return bool
     */
    public function isPasswordExpired(): bool
    {
        $passwordInterval = Cache::get('GlobalConfig', function () {
            return \App\GlobalConfig::first();
        })->passwordResetInterval;

        if ($passwordInterval === 0) {
            return false;
        }

        return now()
            ->subDays($passwordInterval)
            ->greaterThan($this->password_changed_at);
    }

    /**
     * Use NmsPrime Helperfunction to calculate geographical distance between
     * two coordinates.
     *
     * @param float $latitude
     * @param float $longitude
     *
     * @return float [km]
     */
    public function getDistance($latitude, $longitude)
    {
        return distanceLatLong($this->geopos_y, $this->geopos_x, $latitude, $longitude) / 1000;
    }

    /**
     * Check if the Position of the current user is outdated.
     *
     * @return bool
     */
    public function isGeoposOutdated()
    {
        return $this->geopos_updated_at->lte(now()->sub(self::GEOPOS_EXPIRATION_TIME));
    }
}

class UserObserver
{
    public function created($user)
    {
        Bouncer::allow($user)->toOwn(User::class);
        $user->api_token = $user->api_token = \Illuminate\Support\Str::random(80);
        $user->save();
    }

    public function updating($user)
    {
        // Rebuild cached sidebar when user changes his language
        if ($user->isDirty('language')) {
            Session::forget('menu');

            $userLang = checkLocale($user->language);

            App::setLocale($userLang);
            Session::put('language', $userLang);
        }

        if ($user->isDirty('password')) {
            $user->api_token = \Str::random(80);
        }
    }

    public function deleting($user)
    {
        $self = \Auth::user();
        $authRank = $self->getHighestRank();

        if ($authRank == '101') {
            return;
        }

        if ($self->hasSameRankAs($user) || $self->hasLowerRankThan($user)) {
            return false;
        }
    }
}
