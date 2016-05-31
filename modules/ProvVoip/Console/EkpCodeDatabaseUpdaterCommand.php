<?php namespace Modules\Provvoip\Console;

use Log;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use \Modules\ProvVoip\Entities\EkpCode;

/**
 * Class for updating database with ekp codes from csv file
 */
class EkpCodeDatabaseUpdaterCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'provvoip:update_ekp_code_database';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update the database ekp code using the csv file $csv_file';

	/**
	 * The path were the csv file is stored.
	 *
	 * @var string
	 */
	protected $csv_file = 'config/provvoip/ekp_codes.csv';

	/**
	 * Path to hash file for csv comparation.
	 * this path is later used by \Storage::…
	 * if relative: this is stored in …/lara/storage/app
	 *
	 * @var string
	 */
	protected $hash_file = 'config/provvoip/ekp_codes__sha1sum';


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
	 * @return null
	 */
	public function fire()
	{

		// nothing to do
		if (!$this->_have_to_update()) {
			return;
		}

		// if csv in unreadable => do nothing
		if (!$this->_read_csv()) {
			return;
		}

		// all OK? update now!
		$this->_update_table();
	}


	/**
	 * Clears the csv hash file (creates file with no content).
	 * Use this if you have to force updating the table – e.g. in migration process.
	 *
	 * @author Patrick Reichel
	 */
	public function clear_hash_file() {
		\Storage::put($this->hash_file, '');
	}


	/**
	 * Checks if the database has to be updated.
	 * This is a pessimistic check;
	 *
	 * @author Patrick Reichel
	 *
	 * @return True if database has to be updated, False else
	 */
	protected function _have_to_update() {

		// if there is no csv file, we can't update the db…
		if(!\Storage::has($this->csv_file)) {
			Log::warning($this->name.': CSV file with ekp codes does not exist ('.storage_path().'/'.$this->csv_file.')');
			return false;
		}

		// if there is no hash file: update db (in worst case we don't change something)
		if (!\Storage::has($this->hash_file)) {
			return true;
		}

		// check if current csv's checksum differes from the last version
		$cur_hash  = sha1(\Storage::get($this->csv_file));
		$last_hash = \Storage::get($this->hash_file);

		if ($cur_hash == $last_hash) {
			Log::debug($this->name.': CSV file has not changed; no database update necessary');
			return false;
		}

		return true;
	}


	/**
	 * Helper to read csv file to array
	 *
	 * @author Patrick Reichel
	 *
	 * @return True if csv file could be read, else false
	 */
	protected function _read_csv() {

		try {
			$this->csv = array_map('str_getcsv', str_getcsv(\Storage::get($this->csv_file), "\n"));
		}
		catch (Exception $ex) {
			Log:error($this->name.': Something went wrong reading CSV file ('.$this->csv_file.') => '.$ex->getMessage());
			return false;
		}

		return true;
	}


	/**
	 * Updates the database table with CSV values.
	 *
	 * The only actions we perform is adding new and updating existing ekp codes.
	 * The deletion of not contained ekp codes would IMO be a bad idea:
	 *     - we lose data if something went wrong in building the csv
	 *     - some entries in e.g. older phonenumbers refer to non existing (=soft deleted) entries
	 *     - it general should be no problem to hold the old codes – they simply will not be used anymore in future actions
	 *
	 * @author Patrick Reichel
	 */
	protected function _update_table() {

		foreach ($this->csv as $key => $entry) {
			$code = $entry[0];
			$company = $entry[1];

			# alter entry if exists, else create new one
			$cc = EkpCode::firstOrNew(array('ekp_code' => $code));
			$cc->ekp_code = $code;
			$cc->company = $company;
			$cc->save();
		}

		$hash = sha1(\Storage::get($this->csv_file));
		\Storage::put($this->hash_file, $hash);
		Log::info($this->name.': Database ekpcodes updated');

	}

}

