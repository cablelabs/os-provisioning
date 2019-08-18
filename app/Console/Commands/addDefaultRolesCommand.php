<?php

namespace App\Console\Commands;

use Log;
use Bouncer;
use App\Role;
use App\BaseModel;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Add default roles
 *
 * @author Nino Ryschawy
 */
class addDefaultRolesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'auth:roles';

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
    public function handle()
    {
        $roles = self::get_default_roles();
        $roles_permissions = self::get_default_permissions();
        $models = BaseModel::get_models();

        foreach ($roles as $role) {
            if (Role::find($role['id'])) {
                echo 'Warning: Role with ID '.$role['id'].' already exists  ('.$role['name'].") - Discarding\n";
                Log::warning('Discard adding Role \"'.$role['name'].'\" as there already is an entry in DB!');
                continue;
            } else {
                $rules = Role::rules($role['id']);
                $validator = \Validator::make($role, $rules);

                if ($validator->fails()) {
                    echo 'ERROR - Validation Rule Error for Role '.$role['name'].":\n ".$validator->errors()."\n";
                    Log::warning('Validation Rule Error: '.$validator->errors());
                    continue;
                }

                Role::create($role);
                echo "Created role: '".$role['name']."'\n";
            }

            if (! isset($roles_permissions[$role['id']])) {
                echo "No Permissions set\n";
                continue;
            }

            $modules_allowed = $roles_permissions[$role['id']]['modules'];
            $models_allowed = $roles_permissions[$role['id']]['models'];

            // Add all Models that shall have permitions for current role
            foreach ($models as $model) {
                $name = explode('\\', $model);
                $module = $name[0] == 'Modules' ? $name[1] : $name[0];
                $name = end($name);

                if (array_key_exists($module, $modules_allowed)) {
                    $permissions = $modules_allowed[$module];
                } elseif (array_key_exists($name, $models_allowed)) {
                    $permissions = $models_allowed[$name];
                } else {
                    continue;
                }

                if (in_array('manage', $permissions)) {
                    Bouncer::allow($role['name'])->toManage($model);
                } else {
                    if (in_array('view', $permissions)) {
                        Bouncer::allow($role['name'])->to('view', $model);
                    }

                    if (in_array('create', $permissions)) {
                        Bouncer::allow($role['name'])->to('create', $model);
                    }

                    if (in_array('edit', $permissions)) {
                        Bouncer::allow($role['name'])->to('edit', $model);
                    }

                    if (in_array('delete', $permissions)) {
                        Bouncer::allow($role['name'])->to('delete', $model);
                    }
                }

                echo "Added Ability for Role '".$role['name']."' to Model: $name\n";
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
        return [
            [
                'id' => 4,
                'name' => 'technican',
                'rank' => 40,
                'description' => 'Allow only technical aspects',
                ],
            [
                'id' => 5,
                'name' => 'accounting',
                'rank' => 40,
                'description' => 'Only accounting relevant stuff',
            ], ];
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
        $role[4] = [
            'modules' => [
                'Dashboard' 		=> ['view'],
                'HfcBase' 			=> ['view'],
                'HfcCustomer'		=> ['view'],
                'HfcReq' 			=> ['view'],
                'ProvVoipEnvia'		=> ['view'],
            ],
            'models' => [
                'Role' 					=> ['view'],
                'User' 					=> ['view'],
                'GlobalConfig'			=> ['view'],

                'Item' 					=> ['view'],

                'User' 					=> ['view'],

                'Cdr'					=> ['view'],
                'GuiLog' 				=> ['view'],
                'Ticket'				=> ['view'],

                'Cmts' 					=> ['view'],
                'Contract' 				=> ['view'],
                'Modem' 				=> ['view'],
                'ProvBase' 				=> ['view'],

                'Mta' 					=> ['view'],
                'PhonebookEntry' 		=> ['view'],
                'Phonenumber' 			=> ['view'],
                'PhonenumberManagement' => ['view'],
                'ProvVoip' 				=> ['view'],

                'EnviaOrderDocument' 	=> ['view'],
                'ProvVoipEnvia' 		=> ['view'],
                'ProvVoipEnviaHelpers' 	=> ['view'],

                'Parameter' 			=> ['view'],
                'Oid' 					=> ['view'],
                'SnmpValue' 			=> ['view'],
            ], ];

        // accounting
        $role[5] = [
            'modules' => [
                'BillingBase'			=> ['view'],
                'Dashboard'				=> ['view'],
                'Ticketsystem'			=> ['view'],
                ],
            'models' => [
                'Contract'				=> ['manage'],
                'GlobalConfig'			=> ['manage'],
                'GuiLog'				=> ['view'],
                'Modem'					=> ['manage'],
                'Mta'					=> ['manage'],
                'Phonenumber'			=> ['view'],
                'PhonenumberManagement'	=> ['manage'],
                'PhoneTariff'			=> ['view'],
                'ProvBase'				=> ['view'],
            ], ];

        return $role;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // array('example', InputArgument::REQUIRED, 'An example argument.'),
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        ];
    }
}
