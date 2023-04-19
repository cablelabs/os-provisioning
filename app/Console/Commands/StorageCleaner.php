<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;
use Log;
use Nwidart\Modules\Facades\Module;

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
     *  path        absolute path to directory containing data to be compressed/deleted
     *  function    method to call
     * The following keys can be missing – in which case there will be no compressing/deleting
     *  compress    threshold for compressing (used in DateInterval(), e.g. 14D, 6M, 2Y)
     *  delete      threshold for deletion (used in DateInterval(), e.g. 14D, 6M, 2Y)
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
    }

    /**
     * Populate the instance array holding age thresholds for compressing/deleting.
     *
     * @TODO these thresholds are hardcoded in this first step – later on this should be configurable using .env or GlobalConfig
     *
     * @author Patrick Reichel
     */
    protected function prepareMetadata()
    {
        if (Module::collections()->has('ProvVoipEnvia')) {
            $this->prepareEnviaMetadata();
        }

        if (Module::collections()->has('SmartOnt')) {
            $this->prepareSmartOntMetadata();
        }

        // Tmp dir is used for billing & ccc
        $this->thresholds[] = [
            'path' => storage_path('app/tmp/'),
            'function' => 'removeOutdatedFiles',
            'delete' => '6M',
        ];

        if (Module::collections()->has('HfcSnmp')) {
            $this->thresholds[] = [
                'path' => storage_path('app/data/hfc/snmpvalues/'),
                'function' => 'removeOutdatedFiles',
                'delete' => '1M',
            ];
        }
    }

    /**
     * Add data for module ProvVoipEnvia
     *
     * @author Patrick Reichel
     */
    protected function prepareEnviaMetadata()
    {
        $enviaApiXmlThresholds = config('provvoipenvia.StorageCleaner');

        if (! boolval($enviaApiXmlThresholds['compress'])) {
            unset($enviaApiXmlThresholds['compress']); // no compression
        }
        if (! boolval($enviaApiXmlThresholds['delete'])) {
            unset($enviaApiXmlThresholds['delete']); // no deletion
        }

        array_push($this->thresholds, $enviaApiXmlThresholds);
    }

    /**
     * Add data for module SmartOnt
     *
     * @author Patrick Reichel
     */
    protected function prepareSmartOntMetadata()
    {
        $smartOntThresholds = config('smartont.StorageCleaner');

        if (is_null($smartOntThresholds)) {
            return;
        }

        if (! boolval($smartOntThresholds['compress'])) {
            unset($smartOntThresholds['compress']); // no compression
        }
        if (! boolval($smartOntThresholds['delete'])) {
            unset($smartOntThresholds['delete']); // no deletion
        }

        array_push($this->thresholds, $smartOntThresholds);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->prepareMetadata();

        Log::info('Storage cleaner started');

        // process the folders one by one
        foreach ($this->thresholds as $entry) {
            try {
                Log::debug('Calling '.$entry['function'].'() for '.$entry['path']);
                $function = $entry['function'];
                $this->${'function'}($entry);
            } catch (\Exception $ex) {
                if (array_key_exists('path', $entry)) {
                    $path = $entry['path'];
                } else {
                    $path = '(not set)';
                }

                Log::error('ERROR cleaning storage folder '.$path.': '.$ex->getMessage());
            }
        }
    }

    /**
     * Simple method to remove files older than specified timeframe in given directory
     *
     * @author Nino Ryschawy
     */
    protected function removeOutdatedFiles($data)
    {
        if (! array_key_exists('path', $data)) {
            Log::error(__CLASS__.': No path given');

            return;
        }

        if (! File::isDirectory($data['path'])) {
            Log::warning('Path '.$data['path'].' not existing or not a directory');

            return;
        }

        $now = new \DateTime();
        $threshold = $now->sub(new \DateInterval('P'.$data['delete']))->getTimestamp();

        $files = File::allFiles($data['path']);

        foreach ($files as $file) {
            if ($file->getMTime() > $threshold) {
                // echo date('Y-m-d - ', $file->getMTime()).$file->getPathName()."\n";
                continue;
            }

            unlink($file->getPathName());
            $msg = 'Deleted '.$file->getPathName()."\n";

            if ($this->output) {
                echo $msg;
            } else {
                Log::info($msg);
            }
        }
    }

    /**
     * Method for cleanup of monthly folders
     * Processes all directories in given path
     *
     * @author Patrick Reichel
     */
    protected function monthlyFolders($data)
    {
        if (! array_key_exists('path', $data)) {
            Log::error(__CLASS__.': No path given');

            return;
        }

        if (! File::isDirectory($data['path'])) {
            Log::warning('Path '.$data['path'].' not existing or not a directory');

            return;
        }

        // generate the string for compressing (this later on is used by simple < compare)
        if (array_key_exists('compress', $data)) {
            $now = new \DateTime();
            $compress = $now->sub(new \DateInterval('P'.$data['compress']))->format('Y-m');
            if ($compress == date('Y-m')) {
                Log::warning('Compression threshold seems to be set to zero – will not compress');
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
                Log::warning('Deletion threshold seems to be set to zero – will not delete');
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
                (preg_match($dir_regex, $element) == 0) &&
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
                Log::info('Compressing '.$dirpath);
                $tar_cmd = "cd $path && tar -jcvf $archivepath $dir";
                try {
                    $tar_return = `$tar_cmd`;
                    $tar_return = explode("\n", $tar_return);
                    $tar_return_log = array_slice($tar_return, 0, 3);
                    Log::debug('Calling "'.$tar_cmd.'" returned '.count($tar_return).' lines, beginning with '.implode('\n ', $tar_return_log));
                } catch (\Exception $ex) {
                    Log::error("Exception calling '$tar_cmd': ".$ex->getMessage());
                }

                if (File::isFile($archivepath)) {
                    // remove original directory
                    File::deleteDirectory($dirpath);
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
                if (File::isFile($elementpath)) {
                    Log::info('Deleting '.$elementpath);
                    File::delete($elementpath);
                }
                // delete directories older than threshold (e.g. in no compression is wanted)
                elseif (File::isDirectory($elementpath)) {
                    Log::info('Deleting '.$elementpath);
                    File::deleteDirectory($elementpath);
                }
            }
        }
    }
}
