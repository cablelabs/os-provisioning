<?php

namespace App\Http\Controllers\Auth;

use Str;
use Module;
use Bouncer;
use App\Role;
use App\Ability;
use App\BaseModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseViewController;

class AbilityController extends Controller
{
    /**
     * Crud Actions Array, that is used to populate the Ability Blade and to
     * iterate through the various actions in Blade context. As key the
     * Shorthand for abilities is used. Value is an Option array of
     * Properties which are used only inside Blade context.
     *
     * @return Illuminate\Support\Collection
     * @author Christian Schramm
     */
    public static function getCrudActions()
    {
        return collect([
                '*'  => ['name' => 'manage', 'icon' => 'fa-star', 'bsclass' => 'success'],
            'view'   => ['name' => 'view', 'icon' => 'fa-eye', 'bsclass' => 'info'],
            'create' => ['name' => 'create', 'icon' => 'fa-plus', 'bsclass' => 'primary'],
            'update' => ['name' => 'update', 'icon' => 'fa-pencil', 'bsclass' => 'warning'],
            'delete' => ['name' => 'delete', 'icon' => 'fa-trash', 'bsclass' => 'danger'],
        ]);
    }

    /**
     * Updates the Abilities that are not explicitly bound to a model and some
     * Helper Abilities (like "allow all", "view all"). It is bound to the
     * Route "customAbility.update" and called via AJAX Requests.
     *
     * @param Illuminate\Http\Request $requestData
     * @return Illuminate\Support\Collection
     * @author Christian Schramm
     */
    protected function updateCustomAbility(Request $requestData)
    {
        $changedIds = $this->getChangedIds($requestData);

        $role = Role::find($requestData->roleId);
        $abilities = Ability::whereIn('id', $changedIds)->get();

        $this->registerCustomAbility($requestData, $role->name, $abilities);

        return collect([
            'id' => intval($requestData->id) ? $requestData->id : $changedIds,
            'roleAbilities' => self::mapCustomAbilities($role->getAbilities()),
            'roleForbiddenAbilities' => self::mapCustomAbilities($role->getForbiddenAbilities()),
        ])->toJson();
    }

    /**
     * Updates the Abilities that are explicitly bound to a model with the CRUD
     * actions manage (allow everything on that model), view, create, update
     * and delete. It is bound to the Route "modelAbility.update" and is
     * called via AJAX Requests.
     *
     * @param Illuminate\Http\Request $request
     * @return json
     * @author Christian Schramm
     */
    protected function updateModelAbility(Request $request)
    {
        $requestData = collect($request->all())->forget('_token');

        $module = $requestData->pull('module');
        $allowAll = $requestData->pull('allowAll');
        $role = Role::find($requestData->pull('roleId'));

        $modelAbilities = self::getModelAbilities($role)[$module]
            ->keys()
            ->mapWithKeys(function ($model) use ($requestData) {
                if (! $requestData->has($model)) {
                    $requestData[$model] = [];
                }

                return [$model => $requestData[$model]];
            })
            ->merge($requestData);

        $this->registerModelAbilities($role, $modelAbilities, $allowAll);

        return self::getModelAbilities($role)->toJson();
    }

    /**
     * Registers the custom abilities with Bouncer and therefore Laravels Gate
     * with respect to the "allow all" ability. Only changed Abilities are
     * handled to increase the Performance.
     *
     * @param mixed $requestData
     * @param string $roleName
     * @param Illuminate\Database\Eloquent\Collection $abilities
     * @return void
     * @author Christian Schramm
     */
    protected function registerCustomAbility($requestData, string $roleName, $abilities)
    {
        $allowedAbilities = collect($requestData->roleAbilities)->filter();
        $forbiddenAbilities = collect($requestData->roleForbiddenAbilities)->filter();

        foreach ($abilities as $ability) {
            if ($allowedAbilities->has($ability->id)) {
                Bouncer::allow($roleName)->to($ability->name, $ability->entity_type);
            }

            if (! $allowedAbilities->has($ability->id)) {
                Bouncer::disallow($roleName)->to($ability->name, $ability->entity_type);
            }

            if ($forbiddenAbilities->has($ability->id)) {
                Bouncer::forbid($roleName)->to($ability->name, $ability->entity_type);
            }

            if (! $forbiddenAbilities->has($ability->id)) {
                Bouncer::unforbid($roleName)->to($ability->name, $ability->entity_type);
            }
        }

        Bouncer::refresh();
    }

    /**
     * Registers the model CRUD abilities with Bouncer and therefore Laravels
     * Gate with respect to the "allow all" ability. Only changed Abilities
     * are handled to increase the Performance.
     *
     * @param App\Role $role
     * @param Illuminate\Database\Eloquent\Collection $modelAbilities
     * @param string $allowAll
     * @return void
     * @author Christian Schramm
     */
    protected function registerModelAbilities(Role $role, $modelAbilities, $allowAll)
    {
        $models = collect(BaseModel::get_models());

        foreach ($modelAbilities as $model => $permissions) {
            $crudPermissions = self::getCrudActions();

            foreach ($permissions as $permission) {
                $crudPermissions->forget($permission);
                $actions = $allowAll == 'true' && $allowAll != 'undefined' ?
                            collect(['disallow', 'forbid']) :
                            collect(['unforbid', 'allow']);

                $actions->each(function ($action) use ($permission, $role, $models, $model) {
                    if ($permission == '*') {
                        return Bouncer::$action($role->name)->toManage($models[$model]);
                    }

                    return Bouncer::$action($role->name)->to($permission, $models[$model]);
                });
            }

            foreach ($crudPermissions->keys() as $permission) {
                if ($permission == '*') {
                    Bouncer::disallow($role->name)->toManage($models[$model]);
                    Bouncer::unforbid($role->name)->toManage($models[$model]);
                    continue;
                }

                Bouncer::disallow($role->name)->to($permission, $models[$model]);
                Bouncer::unforbid($role->name)->to($permission, $models[$model]);
            }
        }

        Bouncer::refresh();
    }

    /**
     * Get all non-Crud Abilities and Compose a Collection to use in Blade
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @author Christian Schramm
     */
    public static function getCustomAbilities()
    {
        return Ability::whereNotIn('name', self::getCrudActions()->keys())
            ->orWhere('entity_type', '*')
            ->get()
            ->pluck('title', 'id')
            ->map(function ($title) {
                return collect([
                    'title' => $title,
                    'localTitle' => BaseViewController::translate_label($title),
                    'helperText' => trans('helper.'.$title),
                ]);
            });
    }

    /**
     * Compose a Collection of all CRUD Abilities, which can be used to scaffold
     * the Blade. Some Abilities are Grouped by Custom Rules, but mostly the
     * Module Context is used. The Grouping was done to increase the UX.
     *
     * @param App\Role $role
     * @return Illuminate\Database\Eloquent\Collection
     * @author Christian Schramm
     */
    public static function getModelAbilities(Role $role)
    {
        $modelsToExclude = [
            'AccountingRecord', // has no UI/Route associated
            'Dashboard',        // has its own Authorization checks
            'IcingaHostStatus', // has no UI/Route associated
            'IcingaObject',     // has no UI/Route associated
            'ModemHelper',      // has no UI/Route associated
            'SupportRequest',   // authorization makes no sense
        ];

        $modules = Module::collections()->keys();
        $models = collect(BaseModel::get_models())->forget($modelsToExclude);

        $allowedAbilities = $role->getAbilities();
        $isAllowAllEnabled = $allowedAbilities->where('title', 'All abilities')->first();

        $abilities = $isAllowAllEnabled ?
                    self::mapModelAbilities($role->getForbiddenAbilities()) :
                    self::mapModelAbilities($allowedAbilities);

        $allAbilities = Ability::whereIn('id', $abilities->keys())->orderBy('id', 'asc')->get();

        // Grouping GlobalConfig, Authentication and HFC Permissions to increase usability
        $modelAbilities = collect([
            'GlobalConfig' => collect([
                    'BillingBase',
                    'Ccc',
                    'HfcBase',
                    'ProvBase',
                    'ProvVoip',
                ])
                ->filter(function ($name) use ($modules) {
                    return $modules->contains($name);
                })
                ->prepend('GlobalConfig')
                ->push('GuiLog')
                ->push('Sla')
                ->mapWithKeys(function ($name) use ($models, $allAbilities) {
                    return self::getModelActions($name, $models, $allAbilities);
                }),
        ]);

        $modelAbilities['Authentication'] = self::getModelsAndActions('App', $models, $allAbilities);
        $modelAbilities['HFC'] = self::getModelsAndActions('Hfc', $models, $allAbilities);

        foreach ($modules as $module) {
            $modelAbilities[$module] = self::getModelsAndActions($module, $models, $allAbilities);
        }

        $modelAbilities = $modelAbilities->reject(function ($module) {
            return $module->isEmpty();
        });

        return $modelAbilities;
    }

    /**
     * This Method performs a custom sort for Models to Modules. To keep the
     * Ability-Interface clear and concise for the Users.
     *
     * @param string $name
     * @param Illuminate\Support\Collection $models
     * @param Illuminate\Database\Eloquent\Collection $allAbilities
     * @return Illuminate\Support\Collection
     * @author Christian Schramm
     */
    private static function getModelsAndActions(string $name, $models, $allAbilities)
    {
        return $models->filter(function ($class) use ($name) {
            if ($name == 'App') {
                return Str::contains($class, 'App'.'\\');
            }

            if ($name == 'Hfc') {
                return Str::contains($class, '\\'.'Hfc');
            }

            return Str::contains($class, '\\'.$name.'\\');
        })
        ->mapWithKeys(function ($class, $name) use ($models, $allAbilities) {
            return self::getModelActions($name, $models, $allAbilities);
        });
    }

    /**
     * This method returns the assigned Actions for a given Model.
     *
     * @param string $name
     * @param Illuminate\Support\Collection $models
     * @param Illuminate\Database\Eloquent\Collection $allAbilities
     * @return array
     * @author Christian Schramm
     */
    private static function getModelActions(string $name, $models, $allAbilities)
    {
        return [
            $name => $allAbilities
                    ->where('entity_type', $name == 'Role' ? 'roles' : $models->pull($name)) // Bouncer specific
                    ->pluck('name'),
            ];
    }

    /**
     * Check if only one or if multiple Custom Abilities were changed.
     *
     * @param Illuminate\Http\Request $requestData
     * @return bool
     * @author Christian Schramm
     */
    private function getChangedIds($requestData)
    {
        return intval($requestData->id) ?
                collect($requestData->id) :
                collect($requestData->changed)->filter()->keys();
    }

    /**
     * Get All Abilities and return only the non-Crud based ones.
     *
     * @param Illuminate\Database\Eloquent\Collection $abilities
     * @return Illuminate\Support\Collection
     * @author Christian Schramm
     */
    public static function mapCustomAbilities($abilities)
    {
        return $abilities->filter(function ($ability) {
            return self::isCustom($ability);
        })->pluck('title', 'id');
    }

    /**
     * Get All Abilities and return only the Crud based Abilities.
     *
     * @param Illuminate\Database\Eloquent\Collection $abilities
     * @return Illuminate\Support\Collection
     * @author Christian Schramm
     */
    public static function mapModelAbilities($abilities)
    {
        return $abilities->filter(function ($ability) {
            return ! self::isCustom($ability);
        })
                ->map(function ($ability) {
                    return ['id' => $ability->id,
                            'name' => $ability->name,
                            'entity_type' => $ability->entity_type,
                    ];
                })
                ->keyBy('id');
    }

    /**
     * Checks if the given Ability is a Custom one.
     *
     * @param App\Ability $ability
     * @return bool
     * @author Christian Schramm
     */
    private static function isCustom(Ability $ability)
    {
        return Str::startsWith($ability->entity_type, '*') ||
                $ability->entity_type == null ||
                ! self::getCrudActions()->has($ability->name);
    }
}
