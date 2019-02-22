<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * See https://gist.github.com/ivanvermeyen/b72061c5d70c61e86875#file-ensurequeuelistenerisrunning-php
 *
 * NOTE: Changes by Nino Ryschawy:
 * use "queue:work --daemon" instead of "queue:listen" as this needs approximately 10x less the cpu usage
 */
class EnsureQueueListenerIsRunning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:checkup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure that the queue listener is running.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->isQueueListenerRunning()) {
            $this->comment('Queue listener is being started.');
            $pid = $this->startQueueListener();
            $this->saveQueueListenerPID($pid);
        }
        $this->comment('Queue listener is running.');
    }

    /**
     * Check if the queue listener is running.
     *
     * @return bool
     */
    private function isQueueListenerRunning()
    {
        if (! $pid = $this->getLastQueueListenerPID()) {
            return false;
        }
        $process = exec("ps -p $pid -opid=,cmd=");
        $processIsQueueListener = str_contains($process, 'queue:work');

        return $processIsQueueListener;
    }

    /**
     * Get any existing queue listener PID.
     *
     * @return bool|string
     */
    private function getLastQueueListenerPID()
    {
        if (! file_exists(__DIR__.'/queue.pid')) {
            return false;
        }

        return file_get_contents(__DIR__.'/queue.pid');
    }

    /**
     * Save the queue listener PID to a file.
     *
     * @param $pid
     *
     * @return void
     */
    private function saveQueueListenerPID($pid)
    {
        file_put_contents(__DIR__.'/queue.pid', $pid);
    }

    /**
     * Start the queue listener.
     *
     * @return int
     */
    private function startQueueListener()
    {
        $command = 'php '.base_path().'/artisan queue:work --tries=1 --timeout=9999 > /dev/null & echo $!';
        $pid = exec($command);

        \Log::info('Start general queue worker');

        return $pid;
    }
}
