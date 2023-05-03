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

namespace Modules\ProvBase\Console;

use DB;
use Log;
use File;
use Storage;
use Illuminate\Console\Command;

class ExportCsvCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:export_csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports database tables to CSV files';

    /**
     * The signature (defining the optional argument)
     */
    protected $signature = 'nms:export_csv';

    /**
     * The configfile holding information about what to export
     */
    protected $configDir = 'config/provbase/export';
    protected $configFileName = 'csv_export.ini';

    /**
     * The configuration.
     */
    protected $config = [];

    /**
     * The timestamp format to use if none is given
     */
    protected $defaultTimestampFormat = "__c";

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
     *
     * @author Patrick Reichel
     */
    public function handle()
    {
        $this->checkConfigfile();
        $this->readConfig();

        // export the database tables one by one
        foreach ($this->config['dbtables'] as $table => $options) {
            $filenameBase = $options['filename_base'] ?? $table;
            $columns = array_keys($options['columns']);
            $header = array_values($options['columns']);
            $data = $this->getDbData($table, $columns);
            $csvData = $this->prepareData($header, $data);

            $this->storeCsv($filenameBase, $csvData);
        }
    }

    /**
     * Checks if the configfile exists.
     * Otherwise tries to create one from the example file added during installation
     *
     * @author Patrick Reichel
     */
    protected function checkConfigfile()
    {
        $this->configFile = $this->configDir.'/'.$this->configFileName;
        if (! Storage::has($this->configDir)) {
            Storage::makeDirectory($this->configDir);
        }
        if (! Storage::has($this->configFile)) {
            $exampleConfigFile = $this->configFile.'.example';
            if (! Storage::has($exampleConfigFile)) {
                $msg = __METHOD__.'() could not find config or example config file – please create '.$this->configFile;
                Log::error($msg);
                $this->error($msg);
                exit(1);
            }

            Storage::copy($exampleConfigFile, $this->configFile);
            $msg = 'No configfile found – created '.$this->configFile.'. Adapt to your needs.';
            Log::info($msg);
            $this->info($msg);
        }
    }
    /**
     * Reads the config from the ini file
     *
     * @author Patrick Reichel
     */
    protected function readConfig()
    {
        $configFileAbs = storage_path().'/app/'.$this->configFile;
        $this->info('Reading config from '.$configFileAbs);
        $config_raw = parse_ini_file($configFileAbs, true, INI_SCANNER_NORMAL);

        if (! $config_raw) {
            $msg = __METHOD__.'() could not read config from '.$configFileAbs;
            Log::error($msg);
            $this->error($msg);
            exit(1);
        }

        $this->config = $config_raw['csv_exporter_conf'];
        $this->config['dbtables'] = [];
        foreach ($config_raw as $table => $options) {
            if ('csv_exporter_conf' == $table) {
                // this is the general config, not the table specific one
                continue;
            }
            $this->config['dbtables'][$table] = $options;
        }
    }

    /**
     * Get data for given database table and columns
     *
     * @param  string  $table  The database table
     * @param  array  $columns  The columns to get data for
     * @return Illuminate\Support\Collection
     *
     * @author Patrick Reichel
     */
    protected function getDbData($table, $columns)
    {
        return DB::table($table)->select($columns)->whereNull('deleted_at')->get();
    }

    /**
     * Convert raw database data to a format usable for CSV export
     *
     * @param  array  $head  The header used in CSV file
     * @param  array  $entries  The data, row by row
     * @return array
     *
     * @author Patrick Reichel
     */
    protected function prepareData($head, $entries)
    {
        $ret = [];
        $ret[] = $head;
        foreach ($entries as $entry) {
            $row = [];
            foreach ($entry as $column => $value) {
                $row[] = $value;
            }
            $ret[] = $row;
        }

        return $ret;
    }

    /**
     * Writes data to CSV file
     *
     * @param  string  $filenameBase  The base filename
     * @param  array  $data  The data to be written
     *
     * @author Patrick Reichel
     */
    protected function storeCsv($filenameBase, $data)
    {
        $exportDir = $this->config['export_dir'];

        $createDateSubDirs = boolval($this->config['create_date_subdirs'] ?? false);
        if ($createDateSubDirs) {
            $exportDir .= '/'.date('Y').'/'.date('Y-m').'/'.date('Y-m-d');
        }
        if (! File::exists($exportDir)) {
            File::makeDirectory($exportDir, 0750, true);
        }

        $addTimestamp = boolval($this->config['add_timestamp_to_filename'] ?? false);
        $timestampFormat = $this->config['timestamp_format'] ?? $this->defaultTimestampFormat;
        $suffix = $addTimestamp ? date($timestampFormat).'.csv' : '.csv';
        $filename = $filenameBase.$suffix;
        $filename = str_replace(' ', '_', $filename);
        $filename = preg_replace('/[^.+:a-zA-Z0-9_-]+/', '-', $filename);
        $exportFile = $exportDir.'/'.$filename;

        $this->info("Writing $exportFile");
        try {
            $fh = fopen($exportFile, 'w');
            foreach ($data as $line) {
                fputcsv($fh, $line);
            }
        } catch (Exception $ex) {
            $msg = __METHOD__."(): Error writing $exportFile (".$ex->getMessage().')';
        }
    }
}
