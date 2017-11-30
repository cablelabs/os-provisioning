<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use \App\Authrole;

/**
 * Add default roles
 *
 * @author Nino Ryschawy
 */
class addDefaultRolesCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nms:addDefaultRoles';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'add default roles & access permissions';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$roles = self::get_default_roles();
		$role_permissions = self::get_default_permissions();
		$models = \App\Authcore::where('type', 'LIKE', 'model')->get()->all();

		foreach ($roles as $properties)
		{
			// add new Role if the ID does not exist yet in DB
			$role_id = $properties['id'];

			if (Authrole::find($role_id)) {
				echo "Error: Role with ID ".$role_id." already exists - Discard\n";
				\Log::error('Discard adding Role '.$properties['name'].' as there already is an entry in DB!');
				continue;
				// throw new \Exception("Error: Role with ID ".$role_id." already exists", 1);
			}
			else {
				Authrole::create($properties);
				echo "Created role: '".$properties['name']."'\n";
			}

			if (!isset($role_permissions[$role_id])) {
				echo "No Permissions set\n";
				continue;
			}

			$modules_allowed = $role_permissions[$role_id]['modules'];
			$models_allowed  = $role_permissions[$role_id]['models'];

			// Add all Models that shall have permitions for current role
			foreach ($models as $model)
			{
				$name = explode('\\', $model->name);
				$module = $name[0] == 'Modules' ? $name[1] : $name[0];
				$name = end($name);

				if (array_key_exists($module, $modules_allowed))
					$permissions = $modules_allowed[$module];
				else if (array_key_exists($name, $models_allowed))
					$permissions = $models_allowed[$name];
				else
					continue;

				\App\Authmetacore::updateOrCreate([
					'core_id' => $model->id,
					'role_id' => $role_id,
					'view' 	  => isset($permissions['view']) && $permissions['view'],
					'create'  => isset($permissions['create']) && $permissions['create'],
					'edit' 	  => isset($permissions['edit']) && $permissions['edit'],
					'delete'  => isset($permissions['delete']) && $permissions['delete'],
					]);

				// Debugging Output
				echo "Added Permission for Role '".$properties['name']."' to Model: $name\n";
			}
		}

	}

	/**
	 * Data for Role Models
	 *
	 * TODO: translate role names
	 */
	public static function get_default_roles()
	{
		return array(
			array(
				'id' => 3,
				'name' => 'director',
				'type' => 'role',
				'description' => 'Like super_admin but can see Income on Dashboard',
				),
			array(
				'id' => 4,
				'name' => 'technican',
				'type' => 'role',
				'description' => 'Allow only technical aspects',
				),
			array(
				'id' => 5,
				'name' => 'accounting',
				'type' => 'role',
				'description' => 'Only accounting relevant stuff',
			));
	}

	/**
	 * Data of Permissions for the Roles
	 *
	 * NOTE: Module has higher priority
	 *	- so if a module is specified -> all models of this module will have the defined permission(s)
	 */
	public static function get_default_permissions()
	{
		// technican
		$role[4] = array(
			'modules' => [
				'Dashboard' => ['view' => 1],
				'HfcBase' => ['view' => 1],
				'HfcCustomer' => ['view' => 1],
				'HfcReq' => ['view' => 1],
				'ProvVoipEnvia' => ['view' => 1],
			],
			'models' => [
				'AddressFunctionsTrait' => ['view' => 1],
				'Authmetacore' 	=> ['view' => 1],
				'Authrole' 		=> ['view' => 1],
				'Authuser' 		=> ['view' => 1],
				'GlobalConfig' 	=> ['view' => 1],

				// 'BillingBase' 	=> ['view' => 1],
				'Item' 			=> ['view' => 1], // really?

				// 'Ccc' 			=> ['view' => 1],
				'CccAuthuser' 	=> ['view' => 1],

				'Cdr' 			=> ['view' => 1],
				'GuiLog' 		=> ['view' => 1],
				'Ticket' 		=> ['view' => 1],

				'Cmts' 			=> ['view' => 1],
				'Contract' 		=> ['view' => 1],
				'Modem' 		=> ['view' => 1],
				'ProvBase' 		=> ['view' => 1],

				'Mta' 			=> ['view' => 1],
				'PhonebookEntry' => ['view' => 1],
				'Phonenumber' 	=> ['view' => 1],
				'PhonenumberManagement' => ['view' => 1],
				'ProvVoip' 		=> ['view' => 1],

				'EnviaOrderDocument' => ['view' => 1],
				'ProvVoipEnvia' => ['view' => 1],
				'ProvVoipEnviaHelpers' => ['view' => 1],

				'Parameter' 	=> ['view' => 1],
				'Oid' 			=> ['view' => 1],
				'SnmpValue' 	=> ['view' => 1],
			]);

		// accounting
		$role[5] = array(
			'modules' => [
				'BillingBase',
				'Dashboard',
				'Ticketsystem',
				],
			'models' => [
				'Contract',
				'GlobalConfig',
				'GuiLog',
				'Modem',
				'Mta',
				'Phonenumber',
				'PhonenumberManagement',
				'PhoneTariff',
				'ProvBase',
			]);

		return $role;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
