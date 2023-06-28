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
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Log;
use Modules\BillingBase\Entities\Invoice;
use Modules\BillingBase\Entities\Item;
use Modules\BillingBase\Entities\Product;
use Modules\BillingBase\Entities\SepaMandate;
use Modules\BillingBase\Entities\SettlementRun;
use Modules\ProvBase\Entities\Configfile;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Entities\Modem;
use Modules\ProvBase\Entities\Qos;
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
     * TODO: split by modules or think about it.
     *
     * @var string
     */
    protected $signature = 'import:nms
        {systemName : Name of the Database Connection}
        {costcenter : Costcenter ID for all contracts}
        {--ag= : Contact of contract}
        {--invoices="1970-01-01" : Import invoices with settlementruns starting from YYYY-MM-DD}
        {--configfileMap= : Path to file containing an array of ID mapping between old and new configfiles}
        {--qosMap= : Path to file containing an array of ID mapping between old and new QoS\'}
        {--productMap= : Path to file containing an array of ID mapping between old and new products}
        {--productCostcenterMap= : Path to file containing an array with each product.type as keys and costcenter ID as value}
        {--ticketTypeMap= : Path to file containing an array of ID mapping between old and new ticket types}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import NMS Prime database.';

    /**
     * Mapping of old contract ID's to new contract ID's.
     * Used for ticket.ticketable_id
     *
     * @var array
     */
    protected $contractMap = [];

    /**
     * Mapping of old configfile ID's to new configfile ID's.
     *
     * @var array
     */
    protected $configfileMap = [];

    /**
     * Mapping of old MTA ID's to new MTA ID's.
     *
     * @var array
     */
    protected $mtaMap = [];

    /**
     * Mapping of old phonenumber ID's to new phonenumber ID's.
     *
     * @var array
     */
    protected $phonenumberMap = [];

    /**
     * Mapping of old product ID's to new product ID's.
     *
     * @var array
     */
    protected $productMap = [];

    /**
     * Mapping of old QoS ID's to new QoS ID's.
     * Zero allows contracts without a QoS
     *
     * @var array
     */
    protected $qosMap = [0 => 0];

    /**
     * Mapping of old sepamandat ID's to new sepamandate ID's.
     *
     * @var array
     */
    protected $sepaaccount = [];

    /**
     * Mapping of old settlementrun ID's to new settlementrun ID's.
     * Used to add settlementrun_id of invoices
     *
     * @var array
     */
    protected $settlementrunMap = [];

    /**
     * Mapping of old ticket_type ID's to new ticket_type ID's.
     *
     * @var array
     */
    protected $ticketTypeMap = [];

    /**
     * Array of output, that will be returned in the end.
     *
     * @var array
     */
    protected $fyi = [];

    /**
     * Defines the sections for each progress bar.
     *
     * @var ConsoleOutput
     */
    protected $output;

    protected $contractBar;

    protected $itemBar;

    protected $modemBar;

    protected $mtaBar;

    protected $phonenumberBar;

    protected $sepaBar;

    protected $settlementrunBar;

    protected $invoiceBar;

    protected $ticketBar;

    protected $ticketTypeBar;

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
     * TODO: connection of mysql/pgsql
     * TODO: import PDF's
     * TODO: call observers afterwards
     * TODO: add mapping message
     * TODO: check for unique keys
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createMapping();

        // get existing numbers
        // select number from contract where deleted_at is null and (contract_end >= CURDATE() or contract_end is null or contract_end='0000-00-00');
        $numbers = Contract::where(whereLaterOrEqual('contract_end', now()))
            ->whereNull('deleted_at')
            ->pluck('number');

        $newContracts = $this->removeDuplicateContracts($numbers);

        $newSettlementRuns = SettlementRun::on($this->argument('systemName'))
            ->whereNull('deleted_at')
            // TODO: check for settlementrun instead of invoice creation
            ->where('executed_at', '>=', $this->option('invoices'))
            ->get();

        $ticketTypes = TicketType::on($this->argument('systemName'))
            ->whereNull('deleted_at')
            ->get();

        $tickets = Ticket::on($this->argument('systemName'))
            ->whereNull('deleted_at')
            ->where('state', '!=', 'Closed')
            ->where('ticketable_type', Contract::class)
            ->whereNotIn('ticketable_id', $numbers)
            ->with('user')
            ->get();

        $this->createAllProgressBars($newContracts, $newSettlementRuns, $ticketTypes);

        if ($newSettlementRuns) {
            $this->addSettlementRuns($newSettlementRuns);
        }

        foreach ($newContracts as $contractToImport) {
            $contract = $this->addContract($contractToImport);

            if (! $contract) {
                continue;
            }

            $contract = $this->addInvoices($contractToImport, $contract);
            $contract = $this->addItems($contractToImport, $contract);
            $contract = $this->addModems($contractToImport, $contract);
            $contract = $this->addSepas($contractToImport, $contract);

            $contract->push();
        }

        $this->addTicketTypes($ticketTypes);
        $this->addActiveTickets($tickets);

        $this->printImportantInformation();

        $this->callObservers();
    }

    private function getAttributesWithoutId($model)
    {
        return Arr::except($model->getAttributes(), ['id']);
    }

    private function createMapping()
    {
        $this->createMappingFor(
            'qosMap',
            Qos::on($this->argument('systemName'))
            ->where('deleted_at', null)
                ->get(),
            Qos::all(),
            'ds_rate_max',
            'us_rate_max'
        );

        $this->createMappingFor(
            'productMap',
            Product::on($this->argument('systemName'))
            ->where('deleted_at', null)
                ->get(),
            Product::all(),
            'products',
            'price',
            'type',
            'billing_cycle'
        );

        $this->createMappingFor(
            'ticketTypeMap',
            TicketType::on($this->argument('systemName'))
            ->where('deleted_at', null)
                ->get(),
            TicketType::all(),
            'name',
            'description'
        );

        if ($this->option('configfileMap')) {
            $this->createMappingFor(
                'configfileMap',
                Configfile::on($this->argument('systemName'))
                    ->whereNull('deleted_at')
                    ->get(),
                Configfile::all(),
                'text',
                'device',
                'public'
            );
        }
        /*
        else {
            // for dev purpose
            $this->mapConfigfiles(
                Configfile::on($this->argument('systemName'))
                    ->where('deleted_at', null)
                    ->get()
            );
        }
        */
    }

    private function createMappingFor($map, $newEntries, $existingEntries, ...$comparables)
    {
        if ($this->option($map)) {
            if (! file_exists($this->option($map))) {
                $this->error("{$this->option($map)} does not exist!");
            }
            $this->$map = $this->$map + require $this->option($map);

            return;
        }

        foreach ($newEntries as $new) {
            $existingEntries->filter(function ($entry) use ($comparables, $new, $map) {
                foreach ($comparables as $comp) {
                    if ($entry->$comp != $new->$comp) {
                        return;
                    }
                }

                $this->$map[$new->id] = $entry->id;
            });
        }
    }

    private function createAllProgressBars($newContracts, $settlementRuns, $ticketTypes)
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

        $this->settlementrunBar = $this->createProgressBar(
            $settlementRuns->count(),
            'SettlementRuns'
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
    }

    private function createProgressBar($count, $name)
    {
        $bar = new ProgressBar($this->output->section(), $count);
        $bar->setFormat('custom');
        $bar->setMessage($name);
        $bar->start();

        return $bar;
    }

    public function removeDuplicateContracts($existingNumbers)
    {
        // check for relations in options
        $newContracts = Contract::on($this->argument('systemName'))
        ->whereNotIn('number', $existingNumbers)
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
                    $query->whereNull('deleted_at')
                        ->where('created_at', '>=', $this->option('invoices'))
                        ->where('year', '>=', Str::before($this->option('invoices'), '-'))
                        ->where('month', '>=' , Str::after(Str::beforeLast($this->option('invoices'), '-'), '-'));
                },
            ])
            ->withCount([
                'items',
                'modems',
                'mtas',
                'sepamandates',
                'invoices',
            ])
            ->get();

        $numbersToImport = Contract::on($this->argument('systemName'))
            ->where(whereLaterOrEqual('contract_end', now()))
            ->pluck('number');

        $duplicates = $existingNumbers->filter(function ($existingNumber) use ($numbersToImport) {
            return $numbersToImport->contains($existingNumber);
        });

        foreach ($duplicates as $duplicate) {
            $message = "Skipping contract with number {$duplicate}, since it already exists. ";
            Log::warning($message);
            $fyi[] = $message;
        }

        return $newContracts;
    }

    // TODO: add prompt to ask if the user wants to execute this command
    private function addContract($contractToImport)
    {
        $contract = new Contract;
        $columns = \Schema::getColumnListing($contract->getTable());
        // do not import id
        array_shift($columns);

        // set all properties of column with properties of $contractToImport
        foreach ($columns as $column) {
            $contract->{$column} = $contractToImport->{$column};
        }

        $contract->qos_id = $this->qosMap[$contractToImport->qos_id ?? 0];
        $contract->costcenter_id = $this->argument('costcenter');

        $contract->updated_at = now();
        $contract->save();
        $this->contractMap[$contractToImport->id] = $contract->id;

        $this->contractBar->advance();

        return $contract;
    }

    private function addItems($contractToImport, $contract)
    {
        if ($contractToImport->items->isEmpty()) {
            return $contract;
        }

        $items = [];
        foreach ($contractToImport->items as $item) {
            if (! array_key_exists($item->product_id, $this->productMap)) {
                $message = "Skipping Item {$item->id}, since product {$item->product_id} does not exist in {$this->option('productMap')}";
                $this->fyi[] = $message;
                Log::warning($message);
                continue;
            }

            $newItem = new Item($this->getAttributesWithoutId($item));
            $newItem->updated_at = now();

            // TODO: handle costcenter mapping

            $newItem->product_id = $this->productMap[$item->product_id];
            $items[] = $newItem;

            $newItem->saveQuietly();
        }

        // $contract->items()->saveMany(
        //     $items,
        // );

        $this->itemBar->advance(count($items));

        return $contract;
    }

    private function addModems($contractToImport, $contract)
    {
        if ($contractToImport->modems->isEmpty()) {
            return $contract;
        }

        $modems = [];
        foreach ($contractToImport->modems as $modem) {
            $newModem = new Modem($this->getAttributesWithoutId($modem));
            $newModem->updated_at = now();
            $newModem->configfile_id = $this->configfileMap[$modem->configfile_id];
            // there exist modems with qos_id 0 and NULL
            $newModem->qos_id = $this->qosMap[$modem->qos_id ?? 0];
            $modems[] = $newModem;
            $newModem->saveQuietly();
        }

        // in L10 we could use saveManyQuietly
        // $contract->modems()->saveMany(
        //     $modems,
        // );

        $mtaCount = 0;
        $phonenumberCount = 0;
        foreach ($contractToImport->modems as $modem) {
            $mtas = [];
            $phonenumbers = [];
            foreach ($modem->mtas as $mta) {
                $mtas = $this->addMtas($mta, $modem);

                foreach ($mta->phonenumbers as $phonenumber) {
                    $phonenumbers = $this->addPhonenumbers($phonenumber, $mta);
                }
            }

            $mtaCount += count($mtas);
            $phonenumberCount += count($phonenumbers);
        }

        $this->modemBar->advance(count($modems));
        $this->mtaBar->advance($mtaCount);
        $this->phonenumberBar->advance($phonenumberCount);

        return $contract;
    }

    private function addMtas($mta, $modem)
    {
        $newMta = new Mta($this->getAttributesWithoutId($mta));
        $newMta->updated_at = now();
        $newMta->configfile_id = $this->configfileMap[$modem->configfile_id];
        $newMta->modem_id = $modem->id;
        $mtas[] = $newMta;

        $newMta->saveQuietly();

        $this->mtaMap[$mta->id] = $newMta->id;

        return $mtas;
    }

    private function addPhonenumbers($phonenumber, $mta)
    {
        $newPhonenumber = new Phonenumber($this->getAttributesWithoutId($phonenumber));
        $newPhonenumber->updated_at = now();
        $newPhonenumber->mta_id = $this->mtaMap[$mta->id];

        $phonenumbers[] = $newPhonenumber;

        $newPhonenumber->saveQuietly();
        $this->phonenumberMap[$phonenumber->id] = $newPhonenumber->id;
        $this->addPhonenumbermanagement($phonenumber);

        return $phonenumbers;
    }

    private function addPhonenumbermanagement($phonenumber)
    {
        if ($phonenumbermanagement = $phonenumber->phonenumbermanagement) {
            $newPhonenumberManagement = new PhonenumberManagement($this->getAttributesWithoutId($phonenumbermanagement));
            $newPhonenumberManagement->updated_at = now();
            $newPhonenumberManagement->phonenumber_id = $this->phonenumberMap[$phonenumber->id];

            $newPhonenumberManagement->saveQuietly();
        } else {
            $message = "Phonenumber with ID {$phonenumber->id} is missing a Phonenumbermanagement!";
            $fyi[] = $message;
            Log::warning($message);
        }
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
            $sepas[] = $newSepa;
        }

        $contract->sepamandates()->saveMany(
            $sepas,
        );

        $this->sepaBar->advance(count($sepas));

        return $contract;
    }

    private function addSettlementruns($settlementRuns)
    {
        foreach ($settlementRuns as $sr) {
            $newSettlementRun = new SettlementRun($this->getAttributesWithoutId($sr));
            $newSettlementRun->updated_at = now();
            $newSettlementRun->description .= "\r\nThis settlementrun was imported from {$this->argument('systemName')}";
            $newSettlementRun->saveQuietly();

            $this->settlementrunMap[$sr->id] = $newSettlementRun->id;

            $this->settlementrunBar->advance();
        }
    }

    private function addInvoices($contractToImport, $contract)
    {
        if ($contractToImport->invoices->isEmpty()) {
            return $contract;
        }

        $invoices = [];
        foreach ($contractToImport->invoices as $invoice) {
            $newInvoice = new Invoice(Arr::except($invoice->getAttributes(), ['id']));
            $newInvoice->updated_at = now();
            // sepaacount
            $newInvoice->settlementrun_id = $this->settlementrunMap[$invoice->settlementrun_id];
            $newInvoice->contract_id = $this->contractMap[$invoice->contract_id];
            $newInvoice->save();

            $invoices[] = $newInvoice;
        }

        $this->invoiceBar->advance(count($invoices));

        return $contract;
    }

    private function addTicketTypes($ticketTypes)
    {
        foreach ($ticketTypes as $ticketType) {
            $newTicketType = new TicketType($this->getAttributesWithoutId($ticketType));
            $newTicketType->updated_at = now();
            $newTicketType->parent_id = $ticketType->parent_id ? $this->ticketTypeMap[$ticketType->parent_id] : null;
            $newTicketType->save();

            $this->ticketTypeBar->advance();
        }
    }

    private function addActiveTickets($tickets)
    {
        $users = User::where('deleted_at', null)
            ->pluck('id', 'email');

        foreach ($tickets as $ticket) {
            if (! $users->has($ticket->user->email)) {
                $message = "Cannot find user with email '{$ticket->user->email}'. Ticket '{$ticket->name}' has to be created manually!";
                $fyi[] = $message;
                Log::warning($message);

                $this->ticketBar->advance();

                continue;
            }

            $newTicket = new Ticket($this->getAttributesWithoutId($ticket));
            $newTicket->updated_at = now();
            $newTicket->user_id = $users[$ticket->user->email];
            // TODO: set user as option
            // TODO: assigned user null

            match ($ticket->ticketable_type) {
                Contract::class => $newTicket->ticketable_id = $this->contractMap[$ticket->ticketable_id],
                Modem::class => $newTicket->ticketable_id = $this->contractMap[$ticket->ticketable_id],
                //Contact::class => $newTicket->ticketable_id = $this->contactMap[$ticket->ticketable_id],
                //Apartment::class => $newTicket->ticketable_id = $this->apartmentMap[$ticket->ticketable_id],
                //Realty::class => $newTicket->ticketable_id = $this->realtyMap[$ticket->ticketable_id],
                default => $newTicket->ticketable_id = null,
            };

            $newTicket->save();

            $this->ticketBar->advance();
        }
    }

    private function printImportantInformation()
    {
        foreach ($this->fyi as $line) {
            $this->line($line);
        }
    }

    private function callObservers()
    {
        // TODO call observers
        // TODO also copy all invoices somehow
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
            $configfile->name = $newCf->name." {$this->argument('systemName')}";
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
}
