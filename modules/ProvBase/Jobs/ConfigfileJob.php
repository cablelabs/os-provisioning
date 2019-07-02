<?php

namespace Modules\ProvBase\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfigfileJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable;

    protected $filter;

    protected $cfId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filter = null, $cfId = null)
    {
        $this->filter = $filter;
        $this->cfId = $cfId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $configfile = new \Modules\ProvBase\Entities\Configfile;
        if (isset($this->cfId)) {
            $configfile->execute(null, $this->filter);

            return;
        }

        $configfile->execute($this->filter);
    }
}
