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

use App\User;
use Artisan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\BillingBase\Entities\Invoice;
use Modules\BillingBase\Entities\Item;
use Modules\BillingBase\Entities\SepaMandate;
use Modules\BillingBase\Entities\SettlementRun;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Endpoint;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvVoip\Entities\Mta;
use Modules\ProvVoip\Entities\Phonenumber;
use Modules\ProvVoip\Entities\PhonenumberManagement;
use Modules\Ticketsystem\Entities\Ticket;
use Modules\Ticketsystem\Entities\TicketType;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ImportNmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:nms
        {costcenter : Costcenter ID for all contracts}
        {--configfile-map= : Path to file containing an array of ID\'s, mapping old to new configfiles}
        {--C|contact= : Contact of contract}
        {--costcenter-map= : Path to file containing an array of ID\'s, mapping old to new costcenters}
        {--I|invoices="1970-01-01" : Import invoices with settlementruns starting from YYYY-MM-DD}
        {--Q|qos-map= : Path to file containing an array of ID\'s, mapping old to new QoS\'}
        {--P|product-map= : Path to file containing an array of ID\'s, mapping old to new products}
        {--S|sepa-account-map= : Path to file containing an array of ID\'s, mapping old to new SEPA accounts}
        {--T|ticket-type-map= : Path to file containing an array of ID\'s, mapping old to new ticket types}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import NMS Prime database.';

    /**
     * Path of temporary file to list invoice path transformations for the bash command
     *
     * @var string
     */
    private const INVOICE_TRANSFORM_CSV_PATH = '/tmp/transform.csv';

    /**
     * Name of database connection in config/database.php to import data from
     *
     * @var string
     */
    private const DB_IMPORT_CON = 'nms-import';

    /**
     * Default date from when invoices shall be imported
     *
     * @var string
     */
    protected $invoicesFrom = '1970-01-01';

    /**
     * Mappings of old ID's to new ID's for several models
     *
     * @var array
     */
    protected $configfileMap = [];
    protected $contractMap = [];
    protected $costcenterMap = [0 => 0];
    protected $productMap = [];
    protected $qosMap = [0 => 0];
    protected $sepaAccountMap = [];
    protected $settlementrunMap = [];
    protected $ticketTypeMap = [];

    /**
     * Arrays of MACs in lower case to check duplicates
     *
     * @var array
     */
    protected $modemMacs = null;
    protected $mtaMacs = null;

    /**
     * Array of output, that will be returned in the end.
     *
     * @var array
     */
    protected $errorsToResolve = [];

    /**
     * Defines the sections for each progress bar.
     *
     * @var ConsoleOutput
     */
    protected $output;
    protected $contractBar;

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
    public function handle()
    {
        $this->confirmExecution();

        $this->checkOptions();
        $this->createMappings();
        $this->configureProgressBar();

        $this->addSettlementRuns();

        $this->getAllMacs();
        $this->line('Load contracts...');
        $existingContractNrs = Contract::where(whereLaterOrEqual('contract_end', now()))->whereNull('deleted_at')->pluck('number');
        $contracts = $this->getAllContractsToImport();

        $this->contractBar = $this->createProgressBar($contracts->count(), 'Contracts');

        foreach ($contracts as $contractToImport) {
            $this->checkForDuplicateContracts($contractToImport, $existingContractNrs);

            $contract = $this->addContract($contractToImport);
            $contract = $this->addInvoices($contractToImport, $contract);
            $contract = $this->addItems($contractToImport, $contract);
            $contract = $this->addModems($contractToImport, $contract);
            $contract = $this->addSepas($contractToImport, $contract);
        }

        $this->addActiveTickets();
        $this->copyInvoices();
        $this->callObserverFuncs();

        $this->printErrors();
    }

    private function confirmExecution()
    {
        $info = "IMPORTANT!!!  Have the following things been prepared for this import?\n";
        $info .= "    1. env variables for db connection nms-import\n";
        $info .= "    2. ~/.ssh/config entry to remote server named 'nms-import' for copying invoices exists\n";
        $info .= '    3. ALL of the below listed arguments and options are specified';
        $info .= str_replace('import:nms', '', $this->signature);

        if (! $this->confirm($info)) {
            exit;
        }
    }

    /**
     * Handle invalid user input via options
     *
     * @throws \Exception
     */
    private function checkOptions()
    {
        if ($this->option('invoices')) {
            $ts = strtotime($this->option('invoices'));
            throw_if(! $ts, \InvalidOptionException::class, 'Please specify a date in format YYYY-MM-DD when using invoices option');

            $this->invoicesFrom = Carbon::createFromTimestamp($ts)->addMonth();
        }
    }

    private function configureProgressBar()
    {
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %message% %percent:3s%% , %elapsed:6s% , %estimated:-6s% , %memory:6s%');

        $this->output = new ConsoleOutput();
    }

    private function getAttributesWithoutId($model)
    {
        return Arr::except($model->getAttributes(), ['id']);
    }

    private function createMappings()
    {
        $this->createMappingFor('costcenter-map', null, null);
        $this->createMappingFor('product-map', null, null);
        $this->createMappingFor('qos-map', null, null);
        $this->createMappingFor('sepa-account-map', null, null);

        $this->createMappingFor(
            'ticket-type-map',
            TicketType::on(self::DB_IMPORT_CON)->where('deleted_at', null)->get(),
            TicketType::all(),
            'name',
        );

        if ($this->option('configfile-map')) {
            $this->createMappingFor('configfile-map', null, null);
        }
    }

    private function createMappingFor($map, $newEntries, $existingEntries, ...$comparables)
    {
        $varName = str_replace(' ', '', ucwords(str_replace('-', ' ', $map)));
        $varName[0] = strtolower($varName[0]);

        if ($this->option($map)) {
            if (! file_exists($this->option($map))) {
                $this->error("{$this->option($map)} does not exist!");
            }
            $this->$varName = $this->$varName + require $this->option($map);

            return;
        }

        // currently not used but it can be used, when all entries should be imported (dev)
        foreach ($newEntries as $new) {
            $existingEntries->filter(function ($entry) use ($comparables, $new, $varName) {
                foreach ($comparables as $comp) {
                    if ($entry->$comp != $new->$comp) {
                        return;
                    }
                }

                $this->$varName[$new->id] = $entry->id;
            });
        }
    }

    private function createProgressBar($count, $name)
    {
        $bar = new ProgressBar($this->output->section(), $count);
        $bar->setFormat('custom');
        $bar->setMessage($name);
        $bar->start();

        return $bar;
    }

    public function getAllContractsToImport()
    {
        return Contract::on(self::DB_IMPORT_CON)
            ->where(whereLaterOrEqual('contract.contract_end', now()))
            ->with([
                'items' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'items.product' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'modems' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'modems.endpoints' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'modems.mtas' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'sepamandates' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'modems.mtas.phonenumbers' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'modems.mtas.phonenumbers.phonenumbermanagement' => function ($query) {
                    $query->whereNull('deleted_at');
                },
                'invoices' => function ($query) {
                    $query->whereNull('deleted_at')->where('created_at', '>=', $this->invoicesFrom->toDateString());
                },
            ])
            ->withCount([
                'items',
                'modems',
                'mtas',
                'sepamandates',
                'invoices' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->where('created_at', '>=', $this->invoicesFrom->toDateString());
                },
            ])
            // ->limit(100)
            ->get();
    }

    /**
     * Filter contracts with already existing contract number
     */
    public function checkForDuplicateContracts($newContract, $existingNumbers)
    {
        if ($existingNumbers->contains($newContract->number)) {
            $this->errorsToResolve[] = "Skipping contract with number {$newContract->number} as it already exists.";
        }
    }

    /**
     * Retrieve all MAC addresses for later comparison if MAC already exists
     */
    private function getAllMacs()
    {
        $this->modemMacs = Modem::selectRaw('lower(mac) as mac')->pluck('mac');
        $this->mtaMacs = Mta::selectRaw('lower(mac) as mac')->pluck('mac');
    }

    private function addContract($contract)
    {
        $newContract = new Contract($this->getAttributesWithoutId($contract));

        $newContract->qos_id = $this->qosMap[$contract->qos_id ?? 0];
        $newContract->costcenter_id = $this->argument('costcenter');
        $newContract->contact = $this->option('contact');
        $newContract->updated_at = now();
        $newContract->saveQuietly();

        $this->contractMap[$contract->id] = $newContract->id;
        $this->contractBar->advance();

        return $newContract;
    }

    private function addItems($contractToImport, $contract)
    {
        if ($contractToImport->items->isEmpty()) {
            return $contract;
        }

        foreach ($contractToImport->items as $item) {
            if (! array_key_exists($item->product_id, $this->productMap)) {
                $this->errorsToResolve[] = "Skipping Item {$item->id}, since product {$item->product_id} does not exist in {$this->option('product-map')}";

                continue;
            }

            $newItem = new Item($this->getAttributesWithoutId($item));
            $newItem->updated_at = now();
            $newItem->costcenter_id = $this->costcenterMap[$item->costcenter_id];
            $newItem->product_id = $this->productMap[$item->product_id];
            $newItem->contract_id = $contract->id;

            $newItem->saveQuietly();
            // $this->itemBar->advance();
        }

        // $contract->items()->saveMany($items);

        return $contract;
    }

    private function addModems($contractToImport, $contract)
    {
        if ($contractToImport->modems->isEmpty()) {
            return $contract;
        }

        foreach ($contractToImport->modems as $modem) {
            // TODO: This becomes a performance issue for huge systems - a direkt DB query would be slower for small systems but better for huge systems
            if ($this->modemMacs && $this->modemMacs->contains(strtolower($modem->mac))) {
                $this->errorsToResolve[] = "Skipping modem with duplicate MAC {$modem->mac}!";

                continue;
            }

            // TODO: Here we could easily use Base Configfile instead of skipping the modem, but this message should be resolved upfront anyway
            if (! array_key_exists($modem->configfile_id, $this->configfileMap)) {
                $this->errorsToResolve[] = "Skipping modem with ID {$modem->id} as it is missing a configfile on this system!";

                continue;
            }

            $newModem = new Modem($this->getAttributesWithoutId($modem));
            $newModem->updated_at = now();
            $newModem->netelement_id = null;
            $newModem->configfile_id = $this->configfileMap[$modem->configfile_id];
            $newModem->contract_id = $contract->id;
            // there exist modems with qos_id 0 and NULL
            $newModem->qos_id = $this->qosMap[$modem->qos_id ?? 0];
            $newModem->saveQuietly();

            $this->addMtas($modem, $newModem);
            $this->addEndpoints($modem, $newModem);
        }

        return $contract;
    }

    private function addMtas($modem, $newModem)
    {
        foreach ($modem->mtas as $mta) {
            if ($this->mtaMacs && $this->mtaMacs->contains(strtolower($mta->mac))) {
                $this->errorsToResolve[] = "Skipping Mta with douplicate MAC {$mta->mac}!";

                continue;
            }

            $newMta = new Mta($this->getAttributesWithoutId($mta));
            $newMta->updated_at = now();
            $newMta->configfile_id = $this->configfileMap[$mta->configfile_id];
            $newMta->modem_id = $newModem->id;

            $newMta->saveQuietly();

            $this->addPhonenumbers($mta, $newMta);
        }
    }

    private function addEndpoints($modem, $newModem)
    {
        // TODO: Check if IP already is set on an existing endpoint

        foreach ($modem->endpoints as $ep) {
            $newEp = new Endpoint($this->getAttributesWithoutId($ep));
            $newEp->updated_at = now();
            $newEp->modem_id = $newModem->id;

            $newEp->saveQuietly();
        }
    }

    private function addPhonenumbers($mta, $newMta)
    {
        foreach ($mta->phonenumbers as $pn) {
            $newPn = new Phonenumber($this->getAttributesWithoutId($pn));
            $newPn->updated_at = now();
            $newPn->mta_id = $newMta->id;

            $newPn->saveQuietly();

            $this->addPhonenumbermanagement($pn, $newPn);
        }
    }

    private function addPhonenumbermanagement($phonenumber, $newPhonenumber)
    {
        if (! $phonenumber->phonenumbermanagement) {
            $this->errorsToResolve[] = "Phonenumber with ID {$phonenumber->id} is missing a Phonenumbermanagement!";

            return;
        }

        $newPnMgmt = new PhonenumberManagement($this->getAttributesWithoutId($phonenumber->phonenumbermanagement));
        $newPnMgmt->updated_at = now();
        $newPnMgmt->phonenumber_id = $newPhonenumber->id;

        $newPnMgmt->saveQuietly();
    }

    private function addSepas($contractToImport, $contract)
    {
        if ($contractToImport->sepamandates->isEmpty()) {
            return $contract;
        }

        $sepas = [];
        foreach ($contractToImport->sepamandates as $sepa) {
            $newSepa = new SepaMandate($this->getAttributesWithoutId($sepa));
            $newSepa->updated_at = now();
            $newSepa->costcenter_id = $this->costcenterMap[$sepa->costcenter_id];
            $sepas[] = $newSepa;
        }

        $contract->sepamandates()->saveMany($sepas);

        return $contract;
    }

    private function addSettlementRuns()
    {
        $settlementRuns = SettlementRun::on(self::DB_IMPORT_CON)
            ->whereNull('deleted_at')
            // TODO: check for settlementrun instead of invoice creation
            ->where('executed_at', '>=', $this->option('invoices'))
            ->get();

        $settlementrunBar = $this->createProgressBar($settlementRuns->count(), 'SettlementRuns');

        foreach ($settlementRuns as $sr) {
            $newSettlementRun = new SettlementRun($this->getAttributesWithoutId($sr));
            $newSettlementRun->updated_at = now();
            $newSettlementRun->description .= "\r\nThis settlementrun was imported from ".self::DB_IMPORT_CON;
            $newSettlementRun->saveQuietly();

            $this->settlementrunMap[$sr->id] = $newSettlementRun->id;

            $settlementrunBar->advance();
        }
    }

    private function addInvoices($contractToImport, $contract)
    {
        if ($contractToImport->invoices->isEmpty()) {
            return $contract;
        }

        foreach ($contractToImport->invoices as $invoice) {
            $newInvoice = new Invoice(Arr::except($invoice->getAttributes(), ['id']));
            $newInvoice->updated_at = now();
            $newInvoice->contract_id = $this->contractMap[$invoice->contract_id];
            $newInvoice->sepaaccount_id = $this->sepaAccountMap[$invoice->sepaaccount_id];
            $newInvoice->settlementrun_id = $this->settlementrunMap[$invoice->settlementrun_id];
            $newInvoice->saveQuietly();
        }

        return $contract;
    }

    private function addTicketTypes()
    {
        $ticketTypes = TicketType::on(self::DB_IMPORT_CON)
            ->whereNull('deleted_at')
            ->get();

        $ticketTypeBar = $this->createProgressBar($ticketTypes->count(), 'Ticket Types');

        foreach ($ticketTypes as $ticketType) {
            $newTicketType = new TicketType($this->getAttributesWithoutId($ticketType));
            $newTicketType->updated_at = now();
            $newTicketType->parent_id = $ticketType->parent_id ? $this->ticketTypeMap[$ticketType->parent_id] : null;
            $newTicketType->saveQuietly();

            $ticketTypeBar->advance();
        }
    }

    private function addActiveTickets()
    {
        $tickets = Ticket::on(self::DB_IMPORT_CON)
            ->whereNull('deleted_at')
            ->where('state', '!=', 'Closed')
            ->where('ticketable_type', Contract::class)
            ->with('user')
            ->get();

        if ($tickets->isEmpty()) {
            $this->notice('No tickets to import');
        }

        $ticketBar = $this->createProgressBar($tickets->count(), 'Tickets');
        $users = User::where('deleted_at', null)->pluck('id', 'email');

        foreach ($tickets as $ticket) {
            $newTicket = new Ticket($this->getAttributesWithoutId($ticket));
            $newTicket->ticketable_id = $this->contractMap[$ticket->ticketable_id] ?? null;

            if (! $newTicket->ticketable_id) {
                $this->errorsToResolve[] = 'Skip Ticket as we couldn\'t find the new Contract for that ticket '.$ticket->id;

                continue;
            }

            $newTicket->updated_at = now();
            $newTicket->user_id = $users[$ticket->user->email] ?? null;
            // TODO: assigned users ?

            $newTicket->save();

            $ticketBar->advance();
        }
    }

    private function printErrors()
    {
        foreach ($this->errorsToResolve as $line) {
            $this->line($line);
        }
    }

    /**
     * Copy invoices after transforming IDs in directory paths
     *
     * NOTE: make sure to use ssh-copy-id
     */
    private function copyInvoices()
    {
        $this->createInvoiceMapFile();
        $year = Str::before($this->option('invoices'), '-');

        $cmd = 'cat '.self::INVOICE_TRANSFORM_CSV_PATH.' | ssh '.self::DB_IMPORT_CON.' "cat - > '.self::INVOICE_TRANSFORM_CSV_PATH;
        $cmd .= " && find /var/www/nmsprime/storage/app/data/billingbase/invoice/ -name '{$year}_*.pdf' | grep -f <(cut -d';' -f1 ".self::INVOICE_TRANSFORM_CSV_PATH;
        $cmd .= " | sed 's|.*|/invoice/&/{$year}_|') | tar -cz -T- --transform=\\$(sed 's|^\([0-9]\+\);\([0-9]\+\)$|s\|/invoice/\\1/{$year}_\|/invoice/\\2/{$year}_\||' ";
        $cmd .= self::INVOICE_TRANSFORM_CSV_PATH." | tr '\\n' ';')\" | tar -C / -xz";

        exec($cmd);
    }

    private function createInvoiceMapFile()
    {
        file_put_contents(self::INVOICE_TRANSFORM_CSV_PATH, '');

        $invoicesToImport = fopen(self::INVOICE_TRANSFORM_CSV_PATH, 'a');
        foreach ($this->contractMap as $old => $new) {
            fwrite($invoicesToImport, "$old;$new\n");
        }

        fclose($invoicesToImport);
    }

    /**
     * Execute the whole functionality of the observers delayed to improve performance
     */
    private function callObserverFuncs()
    {
        DB::statement("Update modem set hostname = concat('cm-', id) where hostname != concat('cm-', id) and deleted_at is null");
        Artisan::call('nms:cacti');
        Artisan::call('nms:configfile');
        Artisan::call('nms:dhcp');
    }

    // dev purpose
    private function mapConfigfiles($newCfs)
    {
        $cfs = $this->removeCommentsAndWhitespace(Configfile::all());
        $newCfs = $this->removeCommentsAndWhitespace($newCfs);

        // TODO: parent_ids?
        foreach ($newCfs as $newCf) {
            foreach ($cfs as $cf) {
                if (
                    $cf->text == $newCf->text &&
                    $cf->device == $newCf->device &&
                    $cf->public == $newCf->public &&
                    $cf->dashboard == $newCf->dashboard
                ) {
                    $this->configfileMap[$newCf->id] = $cf->id;

                    continue;
                }
            }
            // if this cf does not exist => create new one
            if (array_key_exists($newCf->id, $this->configfileMap)) {
                continue;
            }

            $configfile = new Configfile($this->getAttributesWithoutId($newCf));
            $configfile->name = $newCf->name.' '.self::DB_IMPORT_CON;
            $configfile->save();

            $this->configfileMap[$newCf->id] = $configfile->id;
        }
    }

    /**
     * Ignore newlines, tabs, comments and multiple spaces.
     *
     * @author Roy Schneider
     *
     * @param  Collection  $cfs
     * @return Collection
     */
    private function removeCommentsAndWhitespace($cfs)
    {
        return $cfs->map(function ($cf) {
            $cf->text = preg_replace("/(\r\n|\r|\n|\/\*.*\*\/|\/\/.*$|\#.*$|  +)/", '', trim($cf->text));

            return $cf;
        });
    }

    /**
     * Unused function was prepared by @Roy Schneider and kept for reference
     */
    private function createAllProgressBars($newContracts, $settlementRuns, $ticketTypes, $tickets)
    {
        // use Symfony ProgressBar otherwise the bars will overwrite each other
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %message% %percent:3s%% , %elapsed:6s% , %estimated:-6s% , %memory:6s%');
        $this->output = new ConsoleOutput();

        $this->contractBar = $this->createProgressBar(
            count($newContracts),
            'Contracts'
        );

        $this->itemBar = $this->createProgressBar(
            $newContracts->sum(function ($contract) {
                return $contract->items_count;
            }),
            'Items'
        );

        $this->modemBar = $this->createProgressBar(
            $newContracts->sum(function ($contract) {
                return $contract->modems_count;
            }),
            'Modems'
        );

        $this->mtaBar = $this->createProgressBar(
            $newContracts->sum(function ($contract) {
                return $contract->mtas_count;
            }),
            'Mtas'
        );

        $this->phonenumberBar = $this->createProgressBar(
            $newContracts->sum(function ($contract) {
                $mtas = $contract->mtas()->get();
                if ($mtas->isNotEmpty()) {
                    return $mtas->sum(function ($mta) {
                        return count($mta->phonenumbers);
                    });
                }
            }),
            'Phonenumbers + Phonenumbermanagement'
        );

        $this->sepaBar = $this->createProgressBar(
            $newContracts->sum(function ($contract) {
                return $contract->sepamandates_count;
            }),
            'SepaMandates'
        );

        $this->invoiceBar = $this->createProgressBar(
            $newContracts->sum(function ($contract) {
                return $contract->invoices_count;
            }),
            'Invoices'
        );

        $this->ticketTypeBar = $this->createProgressBar(
            count($ticketTypes),
            'Ticket Types'
        );

        $this->ticketBar = $this->createProgressBar(
            count($tickets),
            'Tickets'
        );
    }
}
