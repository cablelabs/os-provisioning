<?php

namespace Modules\provbase\Console;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Modules\ProvBase\Entities\Modem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class configfileCommand extends Command implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'nms:configfile {filter?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make all configfiles';

    /**
     * Filter (from argument) to only build cable modem or mta configfiles
     *
     * @var string 		cm|mta
     */
    protected $filter = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($filter = '')
    {
        $this->filter = $filter;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->input && $this->argument('filter')) {
            $this->filter = $this->argument('filter');
        }

        $configfile = new \Modules\ProvBase\Entities\Configfile;
        $configfile->execute($this->filter);
    }
}
