<?php

namespace Modules\ProvBase\Console;

use Illuminate\Console\Command;

class DhcpCommand extends Command
{
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
        \Queue::push(new \Modules\ProvBase\Jobs\DhcpJob());
    }
}
