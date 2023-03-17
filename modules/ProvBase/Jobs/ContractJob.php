<?php

namespace Modules\ProvBase\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ContractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! in_array($this->type, ['daily', 'monthly'])) {
            \Log::error('Wrong type specified for ContractJob');

            return;
        }

        \Artisan::call('nms:contract', ['date' => $this->type]);
    }
}
