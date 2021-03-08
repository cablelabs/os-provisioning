<?php

namespace Modules\ProvBase\Jobs;

use Illuminate\Bus\Queueable;
use Modules\ProvBase\Entities\Modem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetCableModemsOnlineStatusJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $modemCountMax = 500;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job
     */
    public function handle()
    {
        Modem::setCableModemsOnlineStatus($this->modemCountMax);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        \Log::error($exception);

        clearFailedJobs('\\SetModemsOnlineStatusJob');
    }
}
