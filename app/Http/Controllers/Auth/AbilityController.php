<?php

namespace App\Http\Controllers\Auth;

use Bouncer, Module, Str;
use App\{Ability, BaseModel, Role};
use Illuminate\Http\Request;
use App\Http\Controllers\{Controller, BaseViewController};

class AbilityController extends Controller
{
	/**
	 * Crud Actions Array, that is used to populate the Ability Blade and to
	 * iterate through the various actions in Blade context. As key the
	 * Shorthand for abilities is used. Value is an Option array of
	 * Properties which are used only inside Blade context.
	 *
	 * @return Collection|string
	 * @author Christian Schramm
	 */
	public static function getAbilityCrudActions()
	{
		return collect([
				'*' => ['name' => 'manage', 'icon' => 'fa-star', 'bsclass' => 'success'],
		    'view' => ['name' => 'view', 'icon' => 'fa-eye', 'bsclass' => 'info'],
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
	 * @param Request $request
	 * @return Collection|mixed
	 * @author Christian Schramm
	 */
	protected function updateCustomAbility(Request $requestData)
	{
		$role = Role::find($requestData->roleId);

		if (intval($requestData->id)) {
			$ability = Ability::withTrashed()->find($requestData->id);
			$this->registerCustomAbility($requestData, $role->name, $ability);
		} else {
			$changedIds = [];
			foreach ($requestData->changed as $id => $hasChanged) {
				if ($hasChanged == null) continue;
				array_push($changedIds, $id);
				$ability = Ability::withTrashed()->find($id);
				$this->registerCustomAbility($requestData, $role->name, $ability);
			}
		}

		Bouncer::refresh();

		return collect([
			'id' => intval($requestData->id) ? $requestData->id : $changedIds ,
			'roleAbilities' => self::mapCustomAbilities($role->getAbilities()),
			'roleForbiddenAbilities' => self::mapCustomAbilities($role->getForbiddenAbilities())
		])->toJson();
	}

	/**
	 * Updates the Abilities that are explicitly bound to a model with the CRUD
	 * actions manage (allow everything on that model), view, create, update
	 * and delete. It is bound to the Route "modelAbility.update" and is
	 * called via AJAX Requests.
	 *
	 * @param Request $request
	 * @return json|string
	 * @author Christian Schramm
	 */
	protected function updateModelAbility(Request $request)
	{
		$requestData = collect($request->all())->forget('_token');
		$module = $requestData->pull('module');
		$allowAll = $requestData->pull('allowAll');
		$role = Role::find($requestData->pull('roleId'));

		$modelAbilities = self::getModelAbilities($role)[$module]
			->mapWithKeys(function ($actions, $model) use ($requestData) {
				if (!$requestData->has($model))
					$requestData[$model] = [];
				return [$model => $requestData[$model]];
			})->merge($requestData);

		$this->registerModelAbilities($role, $modelAbilities, $allowAll);
		Bouncer::refresh();
		$modelAbilities = self::getModelAbilities($role);

		return $modelAbilities->toJson();
	}

	/**
	 * Registers the custom abilities with Bouncer and therefore Laravels Gate
	 * with respect to the "allow all" ability. Only changed Abilities are
	 * handled to increase the Performance.
	 *
	 * @param mixed $requestData
	 * @param string $roleName
	 * @param Ability $ability
	 * @return void
	 * @author Christian Schramm
	 */
	protected function registerCustomAbility($requestData, $roleName, $ability)
	{
		if ($requestData->changed[$ability->id] && array_key_exists($ability->id, $requestData->roleAbilities))
			Bouncer::allow($roleName)->to($ability->name, $ability->entity_type);

		if ($requestData->changed[$ability->id] && !array_key_exists($ability->id, $requestData->roleAbilities))
			Bouncer::disallow($roleName)->to($ability->name, $ability->entity_type);

		if ($requestData->changed[$ability->id] && array_key_exists($ability->id, $requestData->roleForbiddenAbilities))
			Bouncer::forbid($roleName)->to($ability->name, $ability->entity_type);

		if ($requestData->changed[$ability->id] && !array_key_exists($ability->id, $requestData->roleForbiddenAbilities))
			Bouncer::unforbid($roleName)->to($ability->name, $ability->entity_type);
	}

	/**
	 * Registers the model CRUD abilities with Bouncer and therefore Laravels
	 * Gate with respect to the "allow all" ability. Only changed Abilities
	 * are handled to increase the Performance.
	 *
	 * @param Role $role
	 * @param Collection|mixed $modelAbilities
	 * @param mixed $allowAll
	 * @return void
	 * @author Christian Schramm
	 */
	protected function registerModelAbilities(Role $role, $modelAbilities, $allowAll)
	{
		$models = collect(BaseModel::get_models());
		$crudPermissions = self::getAbilityCrudActions();

		foreach ($modelAbilities as $model => $permissions) {
			foreach ($permissions as $permission) {
				$action = ($allowAll  == 'true' && $allowAll != 'undefined') ? ['disallow', 'forbid'] : ['unforbid', 'allow'];
				$crudPermissions->forget($permission);
				$firstAction = $action[0];
				$secondAction = $action[1];

				if ($permission == '*') {
					Bouncer::$firstAction($role->name)->toManage($models[$model]);
					Bouncer::$secondAction($role->name)->toManage($models[$model]);
					continue;
				}

				Bouncer::$firstAction($role->name)->to($permission, $models[$model]);
				Bouncer::$secondAction($role->name)->to($permission, $models[$model]);
			}

			foreach ($crudPermissions as $permission => $options) {
				if ($permission == '*') {
					Bouncer::disallow($role->name)->toManage($models[$model]);
					Bouncer::unforbid($role->name)->toManage($models[$model]);
					continue;
				}

				Bouncer::disallow($role->name)->to($permission, $models[$model]);
				Bouncer::unforbid($role->name)->to($permission, $models[$model]);
			}
		}
	}

	/**
	 * Get all non-Crud Abilities and Compose a Collection to use in Blade
	 *
	 * @return Collection|mixed
	 * @author Christian Schramm
	 */
	public static function getCustomAbilities()
	{
		$customAbilities = Ability::whereNotIn('name', self::getAbilityCrudActions()->keys())
			->orWhere('entity_type', '*')
			->get()
			->pluck('title', 'id')
			->map(function ($title, $id) {
				return collect([
					'title' => $title,
					'localTitle' => BaseViewController::translate_label($title),
					'helperText' => trans('helper.' . $title),
				]);
			});

		return $customAbilities;
	}

	/**
	 * Compose a Collection of all CRUD Abilities, which can be used to scaffold
	 * the Blade. Some Abilities are Grouped by Custom Rules, but mostly the
	 * Module Context is used. The Grouping was done to increase the UX.
	 *
	 * @param Role $role
	 * @return Collection|mixed
	 * @author Christian Schramm
	 */
	public static function getModelAbilities(Role $role)
	{
		$modelsToExclude = [
			'Dashboard',
		];

		$modules = Module::collections()->keys();
		$models = collect(BaseModel::get_models())->forget($modelsToExclude);
		$allowedAbilities = $role->getAbilities();
		$forbiddenAbilities = $role->getForbiddenAbilities();

		$customAbilities = self::mapCustomAbilities($allowedAbilities);
		$customForbiddenAbilities = self::mapCustomAbilities($forbiddenAbilities);
		$isAllowAllEnabled = $customAbilities->has(1);
		$abilities = $isAllowAllEnabled ? self::mapModelAbilities($forbiddenAbilities) : self::mapModelAbilities($allowedAbilities);
		$allAbilities = Ability::withTrashed()->whereIn('id', $abilities->keys())->orderBy('id', 'asc')->get();

		// Grouping GlobalConfig, Authentication and HFC Permissions to increase usability
		$modelAbilities = collect([
			'GlobalConfig' => collect([
				'GlobalConfig','BillingBase','Ccc','HfcBase','ProvBase','ProvVoip','GuiLog'
				])->mapWithKeys(function ($name) use ($models, $allAbilities) {
					return [
						$name => $allAbilities
									->where('entity_type', $models->pull($name))
									->pluck('name')
					];
				})
		]);

		$modelAbilities['Authentication'] = $models->filter(function ($class) {
			return Str::contains($class, 'App');
		})->mapWithKeys(function ($class, $name) use ($models, $allAbilities) {
			return [
				$name => $allAbilities
							->where('entity_type', $name == 'Role' ? 'roles' : $models->pull($name)) // Bouncer specific
							->pluck('name')
			];
		});

		$modelAbilities['HFC'] = $models->filter(function ($class) {
			return Str::contains($class, '\\' . 'Hfc');
		})->mapWithKeys(function ($class, $name) use ($models, $allAbilities) {
			return [
				$name => $allAbilities
							->where('entity_type', $models->pull($name))
							->pluck('name')
			];
		});

		foreach ($modules as $module) {
			$modelAbilities[$module] = $models->filter(function ($class) use ($module) {
				return (Str::contains($class, '\\'. $module . '\\') &&
						!Str::contains($class, '\\' . 'Hfc') &&
						!Str::contains($class, 'App' . '\\'));
			})->mapWithKeys(function ($class, $name) use ($models, $allAbilities) {
				return [
					$name => $allAbilities
								->where('entity_type', $models->pull($name))
								->pluck('name')
				];
			});
		}

		$modelAbilities = $modelAbilities->reject(function ($value, $key) {
			return $value->isEmpty();
		});

		return $modelAbilities;
	}

	/**
	 * Get All Abilities and return only the non-Crud based ones.
	 *
	 * @param Ability $abilities
	 * @return Collection|mixed
	 * @author Christian Schramm
	 */
	public static function mapCustomAbilities($abilities)
	{
		$sortedAbilities = collect();

		$sortedAbilities = $abilities->filter(function ($ability) {
			return (Str::startsWith($ability->entity_type, '*') || $ability->entity_type == null ||
				!self::getAbilityCrudActions()->has($ability->name));
			})
			->pluck('title', 'id');

		return $sortedAbilities;
	}

	/**
	 * Get All Abilities and return only the Crud based Abilities.
	 *
	 * @param Ability $abilities
	 * @return Collection|mixed
	 * @author Christian Schramm
	 */
	public static function mapModelAbilities($abilities)
	{
		$sortedAbilities = collect();

		$sortedAbilities = $abilities->filter(function ($ability) {
				return (!Str::startsWith($ability->entity_type, '*') && $ability->entity_type !== null &&
					self::getAbilityCrudActions()->has($ability->name));
			})->map(function ($ability) {
				return ['id' => $ability->id, 'name' => $ability->name, 'entity_type' => $ability->entity_type];
			})->keyBy('id');

		return $sortedAbilities;
	}
}
