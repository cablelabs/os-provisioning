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
				$this->registerCustomAbility($requestData, $role->name, $ability);
			}
		}

		Bouncer::refreshFor($role);

		return collect([
			'id' => intval($requestData->id) ? $requestData->id : $changedIds ,
			'roleAbilities' => self::mapAbilities($role->getAbilities()),
			'roleForbiddenAbilities' => self::mapAbilities($role->getForbiddenAbilities())
		])->toJson();
	}

	protected function updateModelAbility(Request $requestData)
	{
		dd($requestData->all());
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


	public static function mapAbilities($abilities)
	{
		$sortedAbilities = collect();

		$sortedAbilities['custom'] = $abilities->filter(function ($ability) {
			return (Str::startsWith($ability->entity_type, '*') || $ability->entity_type == null ||
				!in_array($ability->name, ['*', 'view', 'create', 'update', 'delete']));
			})
			->pluck('title', 'id');

		$sortedAbilities['model'] = $abilities->filter(function ($ability) {
				return (!Str::startsWith($ability->entity_type, '*') && $ability->entity_type !== null &&
					in_array($ability->name, ['*', 'view', 'create', 'update', 'delete']));
			})->map(function ($ability) {
				return ['id' => $ability->id, 'name' => $ability->name, 'entity_type' => $ability->entity_type];
			})->keyBy('id');

		return $sortedAbilities;
	}

	public static function getModelAbilities(Role $role, $roleAbilities = null, $roleForbiddenAbilities = null)
	{
		$modules = Module::collections()->keys();
		$models = collect(BaseModel::get_models());

		if (!isset($roleAbilities))
			$roleAbilities = self::mapAbilities($role->getAbilities());

		if (!isset($roleForbiddenAbilities))
			$roleForbiddenAbilities = self::mapAbilities($role->getForbiddenAbilities());

		if (array_key_exists(1, $roleForbiddenAbilities['custom']))
			$allowAll = false;
		else
			$allowAll = true;

		$abilities = $allowAll ?  $roleForbiddenAbilities['model'] : $roleAbilities['model'];

		// Grouping GlobalConfig, Authentication and HFC Permissions
		// into "special" Groups to increase usability
		$modelAbilities = collect([
			'GlobalConfig' => collect([
				'GlobalConfig','BillingBase','Ccc','HfcBase','ProvBase','ProvVoip','GuiLog'
				])->mapWithKeys(function ($name) use ($abilities, $models) {
						return [$name => Ability::withTrashed()->whereIn('id', $abilities->keys())
							->where('entity_type', $models->pull($name))
							->get()->pluck('name')];
				})
		]);

		$modelAbilities['Authentication'] = $models->filter(function ($class) {
			return Str::contains($class, 'App');
		})->mapWithKeys(function ($class, $name) use ($abilities, $models) {
				return [$name => Ability::withTrashed()->whereIn('id', $abilities->keys())
					->where('entity_type', $models->pull($name))
					->get()->pluck('name')];
		});

		$modelAbilities['HFC'] = $models->filter(function ($value, $key) {
			return Str::contains($value, '\\' . 'Hfc');
		})->mapWithKeys(function ($class, $name) use ($abilities, $models) {
				return [$name => Ability::withTrashed()->whereIn('id', $abilities->keys())
					->where('entity_type', $models->pull($name))
					->get()->pluck('name')];
		});

		foreach ($modules as $module) {
			$modelAbilities[$module] = $models->filter(function ($value, $key) use ($module) {
					return (Str::contains($value, '\\'. $module . '\\') &&
							!Str::contains($value, '\\' . 'Hfc') &&
							!Str::contains($value, 'App' . '\\'));
			})->mapWithKeys(function ($class, $name) use ($abilities, $models) {
					return [$name => Ability::withTrashed()->whereIn('id', $abilities->keys())
						->where('entity_type', $models->pull($name))
						->get()->pluck('name')];
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
			->pluck('title', 'id');

		$customAbilities = $customAbilities->map(function ($title, $id) {
			return collect([
				'title' => BaseViewController::translate_label($title),
				'helperText' => trans('helper.' . $title),
			]);
		});

		return $customAbilities;
	}

}
