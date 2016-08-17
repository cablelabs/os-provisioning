<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * This can be used to cleanup the storage folder. E.g. if using the module ProvVoipEnvia man can store all sent and received XML in files.
 * Here you can define age thresholds for compressing this data to tar.bz2 files and for complete deletion.
 *
 * @author Patrick Reichel
 */
class StorageCleaner extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'main:storage_cleaner';


    /**
     * The console command description.
     *
     * @var string
     */
	protected $description = 'This command is used to compress/remove/whatever old data in specific storage folders';


	/**
	 * Holding age thresholds for subfolders.
	 *
	 * There has to be one subarray for each folder to process; these subarrays have to have the following keys:
	 *	path		absolute path to directory containing data to be compressed/deleted
	 *	function	method to call
	 * The following keys can be missing – in which case there will be no compressing/deleting
	 *	compress	threshold for compressing (used in DateInterval(), e.g. 14D, 6M, 2Y)
	 *	delete		threshold for deletion (used in DateInterval(), e.g. 14D, 6M, 2Y)
	 *
	 * @var array
	 */
	protected $thresholds = array();

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

		$this->_prepare_metadata();

    }


	/**
	 * Populate the instance array holding age thresholds for compressing/deleting.
	 *
	 * @TODO these thresholds are hardcoded in this first step – later on this should be configurable using .env or GlobalConfig
	 * @author Patrick Reichel
	 */
	protected function _prepare_metadata() {

		if (\PPModule::is_active('provvoipenvia')) {

			// defaults
			$envia_api_xml_thresholds = array(
				'path' =>storage_path().'/app/data/provvoipenvia/XML', // the base path holding the date subdirs
				'function' => '_monthly_folders', // function to call
				'compress' => '6M', // age threshold for compressing the subdirs
				'delete' => '24M', // age threshold for deleting .tar.bz2 files
			);

			// if also set in .env: overwrite
			if (array_key_exists('PROVVOIPENVIA__STORE_XML_COMPRESS_AGE', $_ENV)) {
				$envia_api_xml_thresholds['compress'] = $_ENV['PROVVOIPENVIA__STORE_XML_COMPRESS_AGE'];
			}
			if (array_key_exists('PROVVOIPENVIA__STORE_XML_DELETE_AGE', $_ENV)) {
				$envia_api_xml_thresholds['delete'] = $_ENV['PROVVOIPENVIA__STORE_XML_DELETE_AGE'];
			}

			array_push($this->thresholds, $envia_api_xml_thresholds);
		}

	}


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		\Log::info('Storage cleaner started');
		// process the folders one by one
		foreach ($this->thresholds as $entry) {

			try {
				$function = $entry['function'];
				$this->${'function'}($entry);
			}
			catch (Exception $ex) {
				if (array_key_exists('path', $entry)) {
					$path = $entry['path'];
				}
				else {
					$path = '(not set)';
				}
				\Log::error('ERROR cleaning storage folder '.$path.': '.$ex->getMessage());
				throw $ex;
			}
		}
    }


	/**
	 * Method for cleanup of monthly folders
	 * Processes all directories in given path 
	 *
	 * @author Patrick Reichel
	 */
	protected function _monthly_folders($data) {

		// generate the string for compressing (this later on is used by simple < compare)
		if (array_key_exists('compress', $data)) {
			$now = new \DateTime();
			$compress = $now->sub(new \DateInterval('P'.$data['compress']))->format('Y-m');
		}
		else {
			$compress = null;
		}

		// generate string for deletion
		// sub works in place so we create a new “now”
		if (array_key_exists('delete', $data)) {
			$now = new \DateTime();
			$delete = $now->sub(new \DateInterval('P'.$data['delete']))->format('Y-m');
		}
		else {
			$delete = null;
		}

		$path = $data['path'];
		$dirs = array();
		$files = array();

		// we only take care of files/dirs if the name starts with 2 and has format YYYY-MM
		$dir_regex = "#^[1-9][0-9]{3}-[01][0-9]$#";
		$file_regex = str_replace('$', '\.tar\.bz2$', $dir_regex);

		// get the base directories content
		foreach (new \DirectoryIterator($path) as $element) {

			// if element doesn't match one of the regexes: ignore
			if (
				(preg_match($dir_regex, $element) == 0)
				&&
				(preg_match($file_regex, $element) == 0)
			) {
				continue;
			}

			if ($element->isDir()) {
				array_push($dirs, $element->getFilename());
			}
			elseif ($element->isFile()) {
				array_push($files, $element->getFilename());
			}
		}

		// compress the folders
		if (!is_null($compress)) {
			foreach ($dirs as $dir) {

				// if above the threshold: ignore
				if ($dir >= $compress) {
					continue;
				}

				// generate names
				$archive = $dir.'.tar.bz2';
				$archivepath = $path.'/'.$archive;
				$dirpath = $path.'/'.$dir;

				// compress
				\Log::info('Compressing '.$dirpath);
				`tar -jcvf $archivepath $dirpath`;

				if (\File::isFile($archivepath)) {
					// remove original directory
					\File::deleteDirectory($dirpath);
					// append archive to files list
					array_push($files, $archive);
				}
			}
		}

		if (!is_null($delete)) {
			foreach ($files as $file) {

				// if above the threshold: ignore
				if ($file >= $delete) {
					continue;
				}

				$filepath = $path.'/'.$file;

				if (\File::isFile($filepath)) {
					\Log::info('Deleting '.$filepath);
					\File::delete($filepath);
				}
			}
		}

	}

}
