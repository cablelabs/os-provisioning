<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * This class can be used to analyze the timeshift between the scheduler time and the system time.
 * You can check this behavior by using the following bash command:
 *     date --iso-8601=seconds; php /var/www/nmsprime/artisan schedule:run; cat /tmp/par__laravel__time_delta_checker_output; date --iso-8601=seconds
 * Then the times in the file are some (typically 2–10) seconds behind the both direct times
 *
 * If you call this command directly (without scheduler) all works fine – try:
 *     date --iso-8601=seconds; /usr/bin/php /var/www/nmsprime/artisan main:time_delta; cat /tmp/par__laravel__time_delta_checker_output; date --iso-8601=seconds
 *
 * We should keep this behavior in mind – e.g. if we need to perform time critical tasks or have out-of-order log entries.
 * Maybe we could create a bug report to laravel – therefore we need to understand the topic more…
 *
 *
 * @author Patrick Reichel
 */
class TimeDeltaChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'main:time_delta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command can be used to show the difference between system and scheduler time';

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
        // write the current “scheduler” time in file
        $content = date('c');
        $content .= "\n";
        $content .= shell_exec('date --iso-8601=seconds');
        file_put_contents('/tmp/par__laravel__time_delta_checker_output', $content);
    }
}
