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
    protected $thresholds = [];

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
    protected function _prepare_metadata()
    {
        if (\Module::collections()->has('ProvVoipEnvia')) {

            // defaults
            $envia_api_xml_thresholds = [
                'path' =>storage_path().'/app/data/provvoipenvia/XML', // the base path holding the date subdirs
                'function' => '_monthly_folders', // function to call
                'compress' => '6M', // age threshold for compressing the subdirs
                'delete' => '24M', // age threshold for deleting .tar.bz2 files
            ];

            // if compression behavior is also set in .env: overwrite
            // don't use $_ENV directly as this will not be populated in scheduled commands
            $tmp_compress = getenv('PROVVOIPENVIA__STORE_XML_COMPRESS_AGE');
            if ($tmp_compress !== false) {	// not in .env ⇒ use defaults
                if (boolval($tmp_compress)) {	// not set to 0
                    $envia_api_xml_thresholds['compress'] = $tmp_compress;
                } else {	// set to 0 ⇒ no compression
                    if (array_key_exists('compress', $envia_api_xml_thresholds)) {
                        unset($envia_api_xml_thresholds['compress']);
                    }
                }
            }

            // if deletion behavior is also set in .env: overwrite
            $tmp_delete = getenv('PROVVOIPENVIA__STORE_XML_DELETE_AGE');
            if ($tmp_delete !== false) {	// not in .env ⇒ use defaults
                if (boolval($tmp_delete)) {	// not set to 0
                    $envia_api_xml_thresholds['delete'] = $tmp_delete;
                } else {	// set to 0 ⇒ no deletion
                    if (array_key_exists('delete', $envia_api_xml_thresholds)) {
                        unset($envia_api_xml_thresholds['delete']);
                    }
                }
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
                \Log::debug('Calling '.$entry['function'].'() for '.$entry['path']);
                $function = $entry['function'];
                $this->${'function'}($entry);
            } catch (Exception $ex) {
                if (array_key_exists('path', $entry)) {
                    $path = $entry['path'];
                } else {
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
    protected function _monthly_folders($data)
    {
        if (! array_key_exists('path', $data)) {
            \Log::error('No path given.');

            return;
        }

        if (! \File::isDirectory($data['path'])) {
            \Log::warning('Path '.$data['path'].' not existing or not a directory');

            return;
        }

        // generate the string for compressing (this later on is used by simple < compare)
        if (array_key_exists('compress', $data)) {
            $now = new \DateTime();
            $compress = $now->sub(new \DateInterval('P'.$data['compress']))->format('Y-m');
            if ($compress == date('Y-m')) {
                \Log::warning('Compression threshold seems to be set to zero – will not compress');
                $compress = null;
            }
        } else {
            $compress = null;
        }

        // generate string for deletion
        // sub works in place so we create a new “now”
        if (array_key_exists('delete', $data)) {
            $now = new \DateTime();
            $delete = $now->sub(new \DateInterval('P'.$data['delete']))->format('Y-m');
            if ($delete == date('Y-m')) {
                \Log::warning('Deletion threshold seems to be set to zero – will not delete');
                $delete = null;
            }
        } else {
            $delete = null;
        }

        $path = $data['path'];
        $dirs = [];
        $files = [];

        // we only take care of files/dirs if the name starts with 2 and has format YYYY-MM
        $dir_regex = '#^[1-9][0-9]{3}-[01][0-9]$#';
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
            } elseif ($element->isFile()) {
                array_push($files, $element->getFilename());
            }
        }

        // compress the folders
        if (! is_null($compress)) {
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
                $tar_cmd = "cd $path && tar -jcvf $archivepath $dir";
                try {
                    $tar_return = `$tar_cmd`;
                    $tar_return = explode("\n", $tar_return);
                    $tar_return_log = array_slice($tar_return, 0, 3);
                    \Log::debug('Calling "'.$tar_cmd.'" returned '.count($tar_return).' lines, beginning with '.implode('\n ', $tar_return_log));
                } catch (Exception $ex) {
                    \Log::error("Exception calling '$tar_cmd': ".$ex->getMessage());
                }

                if (\File::isFile($archivepath)) {
                    // remove original directory
                    \File::deleteDirectory($dirpath);
                    // append archive to files list
                    array_push($files, $archive);
                }
            }
        }

        $elements = array_merge($dirs, $files);

        if (! is_null($delete)) {
            foreach ($elements as $element) {

                // if above the threshold: ignore
                if ($element >= $delete) {
                    continue;
                }

                $elementpath = $path.'/'.$element;

                // delete .tar.bz2 files older than threshold
                if (\File::isFile($elementpath)) {
                    \Log::info('Deleting '.$elementpath);
                    \File::delete($elementpath);
                }
                // delete directories older than threshold (e.g. in no compression is wanted)
                elseif (\File::isDirectory($elementpath)) {
                    \Log::info('Deleting '.$elementpath);
                    \File::deleteDirectory($elementpath);
                }
            }
        }
    }
}
