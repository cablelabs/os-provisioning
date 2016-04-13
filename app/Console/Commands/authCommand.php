<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/*
 * Set default rights for all Modules
 *
 * NOTE: Module Installation requires a technic to re-install or install some modules after
 *       basic "php artisan migrate" script will be run. Patrick set all default right via migration.
 *       But this will not work for update/re-installation of Modules. So this script will do this job
 *
 *       Example: ISP starts with only provbase module, after a year he needs provvoip. Only running
 *                module:migrate will not adapte the required Auth needed for access.
 *
 * NOTE: This Command could be used in the feature to adapt auth/access rights via command line.
 *
 * @autor Torsten Schmidt
 */
class authCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nms:auth';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'update authentication tables and access rights';

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
		// the following “seeding” is needed in every case – even if the seeders will not be run!
		// add each existing model
		foreach (\BaseModel::get_models() as $model) 
		{
			\Authcore::updateOrCreate(['name'=>$model], ['type'=>'model']);
		}

		// add relations meta<->core for role super_admin
		$models = \DB::table('authcores')->select('id')->where('type', 'LIKE', 'model')->get();
		foreach ($models as $model) {
			\Authmetacore::updateOrCreate(
				['core_id' => $model->id,
				 'meta_id' => 1,
				 'view' => 1,
				 'create' => 1,
				 'edit' => 1,
				 'delete' => 1]
			);
		}

		// add relations meta<->core for client every_net
		$nets = \DB::table('authcores')->select('id')->where('type', 'LIKE', 'net')->get();
		foreach ($nets as $net) {
			\Authmetacore::updateOrCreate(
				['core_id' => $net->id,
				 'meta_id' => 2,
				 'view' => 1,
				 'create' => 1,
				 'edit' => 1,
				 'delete' => 1]
			);
		}
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
