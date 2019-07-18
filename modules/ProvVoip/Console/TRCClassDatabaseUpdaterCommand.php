<?php

namespace Modules\ProvVoip\Console;

use Log;
use Illuminate\Console\Command;
use App\Exceptions\NotImplementedException;

/**
 * Class for updating database with TRC class
 * This will be done using data from a web API or – as fallback – from CSV file
 */
class TRCClassDatabaseUpdaterCommand extends Command
{
    // get some methods used by several updaters
    use \App\Console\Commands\DatabaseUpdaterTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'provvoip:update_trc_class_database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the database table containing trc classes';

    /**
     * The path were the csv file is stored.
     *
     * @var string
     */
    protected $csv_file = 'config/provvoip/trc_classes.csv';

    /**
     * Path to hash file for csv comparation.
     * this path is later used by \Storage::…
     * if relative: this is stored in …/nmsprime/storage/app
     *
     * @var string
     */
    protected $hash_file = 'config/provvoip/trc_classes__sha1sum';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        // this comes from config/app.php (key 'url')
        $this->base_url = \Config::get('app.url');

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return null
     */
    public function handle()
    {
        if (\Module::collections()->has('ProvVoipEnvia')) {
            // we get the data directly from envia TEL API
            $this->_update_using_envia_api();
        } else {
            // fallback: get data from file $this->csv_file
            $this->_update_using_file();
        }
    }

    /**
     * Updating trc classs via envia TEL API means a simple call of the method in ProvVoipEnvia
     * The real work is done there
     *
     * @author Patrick Reichel
     */
    protected function _update_using_envia_api()
    {
        Log::info($this->description.' from envia TEL API');

        // getting data from envia TEL instead from file means: file is not current ⇒ delete the hash file
        $this->clear_hash_file();

        // prepare the URL to process via cURL
        $url_suffix = \URL::route('ProvVoipEnvia.cron', ['job' => 'misc_get_keys', 'keyname' => 'trc_class', 'really' => 'True'], false);
        $url = $this->base_url.$url_suffix;

        // fire!
        $this->_perform_curl_request($url);
    }

    /**
     * Updating from a file (fallback if no other methods are available) is performed here.
     *
     * @author Patrick Reichel
     */
    protected function _update_using_file()
    {
        Log::info($this->description.' from CSV file');

        // this has to be done – but ATM there is no need for such behavior
        // you can use EkpCodeDatabaseUpdaterCommand::_update_using_file() as starting point
        throw new NotImplementedException('Updating TRC classes database from CSV file is not yet implemented!');
    }

    /**
     * Clears the csv hash file (creates file with no content).
     * Use this if you have to force updating the table – e.g. in migration process.
     *
     * @author Patrick Reichel
     */
    public function clear_hash_file()
    {
        \Storage::put($this->hash_file, '');
    }
}
