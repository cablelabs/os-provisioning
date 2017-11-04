<?php

namespace Modules\provbase\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Configfile;

class configfileCommand extends Command implements SelfHandling, ShouldQueue {

	use InteractsWithQueue, SerializesModels;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nms:configfile';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'make all configfiles';


	/**
	 * Configfile ID
	 	* 0:  all Modem & MTA Configfiles (CFs) are built
	 	* >0: all related (with children CFs) cfg's are built
	 *
	 * @var integer
	 */
	protected $cf_id = 0;


	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($cf_id = 0)
	{
		$this->cf_id = $cf_id;

		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		\Log::debug(__CLASS__." called with configfile id: $this->cf_id");

		// handle configfile observer functionality via job in background
		if ($this->cf_id)
		{
			// $cf = Configfile::withTrashed()->find($this->cf_id); 	// only if we want to build for deleted CFs - doesnt make sense right now
			$cf = Configfile::find($this->cf_id);

			$cf->build_corresponding_configfiles();
			$cf->search_children(1);

			return;
		}


		// Modem
		$cms = Modem::all();

		$i = 1;
		$num = count ($cms);

		foreach ($cms as $cm)
		{
			echo "CM: create config files: $i/$num \r"; $i++;
			$cm->make_configfile();
		}
		echo "\n";

		// MTA
		if (\PPModule::is_active('provvoip'))
		{
			$mtas = \Modules\ProvVoip\Entities\Mta::all();

			$i = 1;
			$num = count ($mtas);

			foreach ($mtas as $mta)
			{
				echo "MTA: create config files: $i/$num \r"; $i++;
				$mta->make_configfile();
			}
			echo "\n";
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
			// array('configfile_id', InputArgument::OPTIONAL, 'ID of Configfile - build all related CMs and MTAs for that and all children CFs'),
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
			// array('configfile_id', null, InputOption::VALUE_OPTIONAL, 'ID of Configfile - build all related CMs and MTAs for that and all children CFs, e.g. 1', 0),
		);
	}

}
