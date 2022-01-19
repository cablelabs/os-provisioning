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

namespace Modules\Dreamfiber\Console;

use DB;
use Log;
use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Contract;

class OtoFromCsvUpdaterCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dreamfiber:update_oto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update OTO (=Contract) from daily SEP CSV file.';

    /**
     * Path to hash file for csv comparation.
     * this path is later used by \Storage::…
     * if relative: this is stored in …/nmsprime/storage/app
     *
     * @var string
     */
    protected $hashFile = 'data/dreamfiber/sep.csv.sha1sum';

    /**
     * Status for OTO not (longer) in csv file
     *
     * @var string
     */
    protected $markerNotInCsv = 'NOT IN CSV FILE';

    /**
     * Data read from csv file
     *
     * @var array
     */
    protected $sepData = null;

    /**
     * Header read from csv file
     *
     * @var array
     */
    protected $sepHead = null;

    /**
     * The country code to use if not given
     *
     * @var string
     */
    protected $defaultCountryCode = null;

    /**
     * Holds SEP ID we got data from CSV.
     *
     * @var array
     */
    protected $processedSepIds = null;

    /**
     * Create a new command instance.
     *
     * @return void
     *
     * @author Patrick Reichel
     */
    public function __construct()
    {
        $this->sepFile = config('dreamfiber.api.sepFile');
        parent::__construct();

        if (! \Storage::has($this->hashFile)) {
            \Storage::put($this->hashFile, 'Freshly created.');
        }

        $globalConfig = \App\GlobalConfig::first();
        $this->defaultCountryCode = $globalConfig->default_country_code;
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
        Log::info("Running $this->name – ".__METHOD__.'()');

        if (! $this->updateNeeded()) {
            Log::info("Exiting $this->name");

            return;
        }

        $this->readCsv();
        $this->createOrUpdateOto();
        $this->updateMissingOto();

        \Storage::put($this->hashFile, sha1($this->sepFile));
    }

    /**
     * Read CSV file to an array
     *
     * @author Patrick Reichel
     */
    protected function readCsv()
    {
        try {
            $handle = file($this->sepFile);
            $this->sepData = [];
            foreach ($handle as $line) {
                $this->sepData[] = str_getcsv($line, ';');
            }
            /* $this->sepData = array_map('str_getcsv', file($this->sepFile), ";"); */
        } catch (\Exception $ex) {
            $msg = 'Something went wrong reading CSV file ('.$this->sepFile.') => '.$ex->getMessage();
            $this->error($msg);
            Log::error($msg);
            exit(1);
        }

        $this->sepHead = array_flip(array_shift($this->sepData));
    }

    /**
     * Update contract table with data read from csv file.
     *
     * @author Patrick Reichel
     */
    protected function createOrUpdateOto()
    {
        $processedSepIds = [];

        foreach ($this->sepData as $oto) {
            $sepId = $oto[$this->sepHead['SEP_ID']];

            $contract = Contract::firstOrNew([
                'sep_id' => $sepId,
                'type' => 'DF_OTO',
            ]);

            $contract->street = $oto[$this->sepHead['Location_Street_Name']];
            $contract->house_number = $oto[$this->sepHead['Location_Street_Number']];
            if ($oto[$this->sepHead['Location_Street_Number_Suffix']]) {
                $contract->house_number .= ' '.$oto[$this->sepHead['Location_Street_Number_Suffix']];
            }
            $contract->zip = $oto[$this->sepHead['Location_ZIP']];
            $contract->city = $oto[$this->sepHead['Location_City']];
            $contract->country_code = $this->defaultCountryCode;
            $contract->oto_id = $oto[$this->sepHead['OTO_ID']];
            $contract->oto_port = $oto[$this->sepHead['OTO_Port']];
            $contract->oto_socket_usage = $oto[$this->sepHead['OTO_Socket_Usage']];
            $contract->oto_status = $oto[$this->sepHead['OTO_Status']];
            $contract->flat_id = $oto[$this->sepHead['Flat_ID']];
            $contract->alex_status = $oto[$this->sepHead['ALEX_Status']];
            $contract->omdf_id = $oto[$this->sepHead['OMDF_ID']];
            $contract->boc_label = $oto[$this->sepHead['BOC_Label']];
            $contract->bof_label = $oto[$this->sepHead['BOF_Label']];

            $contract->save();

            $this->processedSepIds[] = $sepId;
        }
        $this->processedSepIds = array_flip($this->processedSepIds);
    }

    /**
     * Update status on OTO not longer in CSV file.
     *
     * @TODO maybe enough to check once a month?
     * @TODO should we soft delete instead?
     *
     * @author Patrick Reichel
     */
    protected function updateMissingOto()
    {
        $sepIds = DB::table('contract')
            ->whereType('DF_OTO')
            ->where('alex_status', '<>', $this->markerNotInCsv)
            ->where('oto_status', '<>', $this->markerNotInCsv)
            ->pluck('sep_id');

        foreach ($sepIds as $sepId) {
            if (array_key_exists($sepId, $this->processedSepIds)) {
                continue;
            }
            $contract = Contract::where('sep_id', '=', $sepId)->first();
            $msg = "Contract $contract->id: SEP ID $sepId not in CSV file – setting status to “".$this->markerNotInCsv.'”';
            Log::warning($msg);
            $this->comment($msg);
            $contract->alex_status = $this->markerNotInCsv;
            $contract->oto_status = $this->markerNotInCsv;
            $contract->save();
        }
    }

    /**
     * Check if DB update is necessary.
     *
     * @return bool true if changes else false
     *
     * @author Patrick Reichel
     */
    protected function updateNeeded()
    {
        // check if file exists
        if (! file_exists($this->sepFile)) {
            $msg = "$this->sepFile does not exist – will not update database.";
            $this->error($msg);
            Log::error($msg);

            return false;
        }

        // check if file can be read
        if (! is_readable($this->sepFile)) {
            $msg = "Cannot read $this->sepFile – will not update database.";
            $this->error($msg);
            Log::error($msg);

            return false;
        }

        // check if file has changed
        if (sha1($this->sepFile) == \Storage::get($this->hashFile)) {
            $msg = "$this->sepFile has not changed – will not update database.";
            $this->line($msg);
            Log::info($msg);

            return false;
        }

        return true;
    }
}
