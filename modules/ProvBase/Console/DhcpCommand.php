<?php

namespace Modules\ProvBase\Console;

use Illuminate\Console\Command;
use Modules\ProvBase\Traits\DhcpCommandTrait;

class DhcpCommand extends Command
{
    use DhcpCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:dhcp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make the DHCP config';

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
     * Execute the console command - Create global Config & all Entries for Modems, Endpoints & Mtas to get an IP from Server
     *
     * @return mixed
     */
    public function handle()
    {
        // if module HA not enabled: queue
        if (! \Module::collections()->has('ProvHA')) {
            $this->doQueueCommand();

            return;
        }

        // if not a slave machine: queue
        if ('slave' != config('provha.hostinfo.ownState')) {
            $this->doQueueCommand();

            return;
        }

        // on HA slave: execute (is not allowed to write to database and therefore not allowed to queue
        $this->doExecuteCommand();
    }

    /**
     * Push to queue for asyncronous, non-blocking execution.
     */
    private function doQueueCommand()
    {
        \Queue::push(new \Modules\ProvBase\Jobs\DhcpJob());
    }

    /**
     * Execute the command directly.
     */
    private function doExecuteCommand()
    {
        $this->executeCommand();
    }
}
