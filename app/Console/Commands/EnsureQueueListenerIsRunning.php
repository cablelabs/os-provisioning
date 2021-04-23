<?php

namespace App\Console\Commands;

use Module;
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

    protected $workers = [
        'low' => [ // handle all low priority tasks - e.g. SettlementRunJob
            'dependency' => 'BillingBase',
        ],
        'high,medium' => [], // handle all high and medium priority tasks - queue 'high' is prioritised - e.g. ConfigfileJob
        'snmpValues' => [ // handle snmp values broadcasting
            'dependency' => 'HfcSnmp',
        ],
    ];

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
        foreach ($this->workers as $name => $properties) {
            if (array_key_exists('dependency', $properties) && ! Module::collections()->has($properties['dependency'])) {
                continue;
            }

            if (! $this->isWorkerRunning($name)) {
                $this->comment("$name worker is being started.");

                $pid = $this->startWorker($name);
                $this->saveWorkerPid($pid, $name);
            }

            $this->comment("$name worker is running.");
        }
    }

    /**
     * Check if the queue listener is running.
     *
     * @param string    name of worker
     * @return bool
     */
    private function isWorkerRunning($name)
    {
        if (! $pid = $this->getLastWorkerPid($name)) {
            return false;
        }

        $process = exec("ps -p $pid -opid=,cmd=");
        $processIsQueueListener = \Str::contains($process, 'queue:work');

        return $processIsQueueListener;
    }

    /**
     * Get any existing queue listener PID.
     *
     * @return bool|string
     */
    private function getLastWorkerPid($name)
    {
        if (! file_exists(__DIR__.'/queue.pid')) {
            return false;
        }

        $pids = json_decode(file_get_contents(__DIR__.'/queue.pid'));

        if (! is_object($pids) || ! isset($pids->$name)) {
            return false;
        }

        return $pids->$name;
    }

    /**
     * Save the queue listener PID to a file.
     *
     * @param $pid
     *
     * @return void
     */
    private function saveWorkerPid($pid, $name)
    {
        $pids = json_decode(file_get_contents(__DIR__.'/queue.pid'));

        if (! is_object($pids)) {
            $pids = [];
        }

        $pids->$name = $pid;

        file_put_contents(__DIR__.'/queue.pid', json_encode($pids));
    }

    /**
     * Start the queue listener.
     *
     * @return int
     */
    private function startWorker($name)
    {
        $command = 'php '.base_path()."/artisan queue:work --queue=$name --tries=1 --timeout=9999 > /dev/null & echo $!";
        $pid = exec($command);

        \Log::info("Start queue worker '$name'");

        return $pid;
    }
}
