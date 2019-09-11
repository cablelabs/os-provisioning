<?php

namespace Modules\OverdueDebts\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportDebtCommand extends Command
{
    /**
     * The console command & table name, description
     *
     * @var string
     */
    public $name = 'debt:import';
    protected $description = 'Import overdue debts from csv';
    protected $signature = 'debt:import {file}';

    const C_NR = 0;
    const VOUCHER_NR = 4;
    const DATE = 5;
    const AMOUNT = 9;
    const DESC = 10;
    const DUN_DATE = 11;
    const INDICATOR = 12;

    /**
     * Execute the console command
     *
     * Create Invoices, Sepa xml file(s), Accounting and Booking record file(s)
     */
    public function handle()
    {
        $arr = file($this->argument('file'));

        unset($arr[0]);

        $num = count($arr);
        $bar = $this->output->createProgressBar($num);

        echo "Import overdue debts\n";
        \Log::info("Import $num overdue debts");
        $bar->start();

        foreach ($arr as $line) {
            $bar->advance();

            $this->addDebt($line);
        }

        $bar->finish();
        echo "\n";

        foreach ($this->errors as $msg) {
            $this->error($msg);
        }
    }

    private function addDebt($line)
    {
        $line = str_getcsv($line, ';');

        $contract = \Modules\ProvBase\Entities\Contract::where('number', $line[self::C_NR])->first();

        if (! $contract) {
            $this->errors[] = 'Could not find contract with number '.$line[self::C_NR];

            return;
        }

        \Modules\OverdueDebts\Entities\Debt::create([
            'contract_id' => $contract->id,
            'voucher_nr' => $line[self::VOUCHER_NR],
            'amount' => str_replace(',', '.', $line[self::AMOUNT]),
            'date' => date('Y-m-d', strtotime($line[self::DATE])),
            'dunning_date' => $line[self::DUN_DATE] ? date('Y-m-d', strtotime($line[self::DUN_DATE])) : null,
            'description' => $line[self::DESC],
            'indicator' => $line[self::INDICATOR] > 0 ? $line[self::INDICATOR] : 0,
            ]);
    }

    /**
     * Get the console command arguments / options
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['file', InputArgument::REQUIRED, 'Filepath of CSV with data to import'],
        ];
    }

    protected function getOptions()
    {
        return [
            // array('debug', null, InputOption::VALUE_OPTIONAL, 'Print Debug Output to Commandline (1 - Yes, 0 - No (Default))', 0),
        ];
    }
}
