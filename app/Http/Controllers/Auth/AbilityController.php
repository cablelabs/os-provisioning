<?php

namespace App\Http\Controllers\Auth;

use Bouncer, Module, Str;
use App\{Ability, BaseModel, Role};
use Illuminate\Http\Request;
use App\Http\Controllers\{Controller, BaseViewController};

class AbilityController extends Controller
{
	/**
	 *
	 * @param Request $request
	 * @return mixed|string
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
				$this->registerCustomAbility($requestData, $role, $ability);
			}
		}

		Bouncer::refreshFor($role);

		return collect([
			'id' => intval($requestData->id) ? $requestData->id : $changedIds ,
			'roleAbilities' => self::mapCustomAbilities($role->getAbilities()),
			'roleForbiddenAbilities' => self::mapCustomAbilities($role->getForbiddenAbilities())
		])->toJson();
	}

	protected function updateModelAbility(Request $request)
	{
		$requestData = collect($request->all())->forget('_token');
		$crudPermissions = self::getAbilityCrudActionsArray();
		$module = $requestData->pull('module');
		$allowAll = $requestData->pull('allowAll');
		$role = Role::find($requestData->pull('roleId'));
		$models = collect(BaseModel::get_models());

		$modelAbilities = self::getModelAbilities($role)[$module]
			->mapWithKeys(function ($value, $key) use ($requestData) {
				if (!$requestData->has($key))
					$requestData[$key] = [];

				return [$key => $requestData[$key]];
			})->merge($requestData);

		foreach ($modelAbilities as $model => $permissions) {
			foreach ($permissions as $permission) {
				$action = $allowAll ? 'forbid' : 'allow';
				$crudPermissions->forget($permission);

				if ($permission == '*') {
					Bouncer::$action($role->name)->toManage($models[$model]);
					continue;
				}

				Bouncer::$action($role->name)->to($permission, $models[$model]);
			}
			foreach ($crudPermissions as $permission => $options) {
				$action = $allowAll ? 'unforbid' : 'disallow';

				if ($permission == '*') {
					Bouncer::$action($role->name)->toManage($models[$model]);
					continue;
				}

				Bouncer::$action($role->name)->to($permission, $models[$model]);
			}
		}

		Bouncer::refreshFor($role);

		$modelAbilities = self::getModelAbilities($role);

		return $modelAbilities->toJson();
	}

	protected function registerCustomAbility($requestData, $role, $ability)
	{
		if ($requestData->changed[$ability->id] && array_key_exists($ability->id, $requestData->roleAbilities))
			Bouncer::allow($role)->to($ability->name, $ability->entity_type);

		if ($requestData->changed[$ability->id] && !array_key_exists($ability->id, $requestData->roleAbilities))
			Bouncer::disallow($role)->to($ability->name, $ability->entity_type);

		if ($requestData->changed[$ability->id] && array_key_exists($ability->id, $requestData->roleForbiddenAbilities))
			Bouncer::forbid($role)->to($ability->name, $ability->entity_type);

		if ($requestData->changed[$ability->id] && !array_key_exists($ability->id, $requestData->roleForbiddenAbilities))
			Bouncer::unforbid($role)->to($ability->name, $ability->entity_type);
	}


	public static function mapCustomAbilities($abilities)
	{
		$sortedAbilities = collect();

		$sortedAbilities['custom'] = $abilities->filter(function ($ability) {
			return (Str::startsWith($ability->entity_type, '*') || $ability->entity_type == null ||
				!in_array($ability->name, ['*', 'view', 'create', 'update', 'delete']));
			})
			->pluck('title', 'id');

		return $sortedAbilities;
	}

	public static function mapModelAbilities($abilities)
	{
		$sortedAbilities = collect();

		$sortedAbilities['model'] = $abilities->filter(function ($ability) {
				return (!Str::startsWith($ability->entity_type, '*') && $ability->entity_type !== null &&
					in_array($ability->name, ['*', 'view', 'create', 'update', 'delete']));
			})->map(function ($ability) {
				return ['id' => $ability->id, 'name' => $ability->name, 'entity_type' => $ability->entity_type];
			})->keyBy('id');

		return $sortedAbilities;
	}

	public static function getModelAbilities(Role $role)
	{
		$modules = Module::collections()->keys();
		$models = collect(BaseModel::get_models());
		$allowedAbilities = $role->getAbilities();
		$forbiddenAbilities = $role->getForbiddenAbilities();

		$customAbilities = self::mapCustomAbilities($allowedAbilities);
		$customForbiddenAbilities = self::mapCustomAbilities($forbiddenAbilities);
		$allowAll = (array_key_exists(1, $customForbiddenAbilities['custom'])) ? false : true;
		$abilities = $allowAll ? self::mapModelAbilities($forbiddenAbilities) : self::mapModelAbilities($allowedAbilities);

		// Grouping GlobalConfig, Authentication and HFC Permissions
		// into "special" Groups to increase usability
		$modelAbilities = collect([
			'GlobalConfig' => collect([
				'GlobalConfig','BillingBase','Ccc','HfcBase','ProvBase','ProvVoip','GuiLog'
				])->mapWithKeys(function ($name) use ($abilities, $models) {
						return [$name => Ability::withTrashed()->whereIn('id', $abilities->keys())
							->where('entity_type', $models->pull($name))
							->orderBy('id', 'asc')
							->get()
							->pluck('name')
						];
				})
		]);

		$modelAbilities['Authentication'] = $models->filter(function ($class) {
			return Str::contains($class, 'App');
		})->mapWithKeys(function ($class, $name) use ($abilities, $models) {
				return[$name => Ability::withTrashed()->whereIn('id', $abilities->keys())
					->where('entity_type', $name == 'Role' ? 'roles' : $models->pull($name)) // Bouncer specific
					->orderBy('id', 'asc')
					->get()
					->pluck('name')
				];
		});

		$modelAbilities['HFC'] = $models->filter(function ($value, $key) {
			return Str::contains($value, '\\' . 'Hfc');
		})->mapWithKeys(function ($class, $name) use ($abilities, $models) {
				return [$name => Ability::withTrashed()->whereIn('id', $abilities->keys())
					->where('entity_type', $models->pull($name))
					->orderBy('id', 'asc')
					->get()
					->pluck('name')
				];
		});

		foreach ($modules as $module) {
			$modelAbilities[$module] = $models->filter(function ($value, $key) use ($module) {
					return (Str::contains($value, '\\'. $module . '\\') &&
							!Str::contains($value, '\\' . 'Hfc') &&
							!Str::contains($value, 'App' . '\\'));
			})->mapWithKeys(function ($class, $name) use ($abilities, $models) {
					return [$name => Ability::withTrashed()->whereIn('id', $abilities->keys())
						->where('entity_type', $models->pull($name))
						->orderBy('id', 'asc')
						->get()
						->pluck('name')
					];
			});
		}

		$modelAbilities = $modelAbilities->reject(function ($value, $key) {
			return $value->isEmpty();
		});

		return $modelAbilities;
	}

	public static function getCustomAbilities()
	{
		$customAbilities = Ability::whereNotIn('name', ['*', 'view', 'create', 'update', 'delete'])
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

	public static function getAbilityCrudActionsArray()
	{
		return collect([
				'*' => ['name' => 'manage', 'icon' => 'fa-star', 'bsclass' => 'success'],
		    'view' => ['name' => 'view', 'icon' => 'fa-eye', 'bsclass' => 'info'],
		    'create' => ['name' => 'create', 'icon' => 'fa-plus', 'bsclass' => 'primary'],
		    'update' => ['name' => 'update', 'icon' => 'fa-pencil', 'bsclass' => 'warning'],
		    'delete' => ['name' => 'delete', 'icon' => 'fa-trash', 'bsclass' => 'danger'],
		]);
	}

}
