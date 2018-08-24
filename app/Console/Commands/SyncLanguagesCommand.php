<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;

class SyncLanguagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nms:synclanguages ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command was created to sync 2 languages and is just here in case it is needed anytime in the future....';

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
        $dir = base_path('resources/lang/de');
        $languagefiles = collect(glob($dir.'/*.php'))
                    ->map(function ($path) {
                        return collect(explode('/', $path))->last();
                    })
                    ->reject('validation.php');

        foreach ($languagefiles as $languagefile) {
            $originalPath = base_path('resources/lang/en/').$languagefile;
            $originalArray = collect(require($originalPath));
            $originalFile = collect(file($originalPath));
            $foreign = file(base_path('resources/lang/de/').$languagefile);
            $modified = false;

            foreach ($foreign as $number => $content) {
                if (! str_contains($content, '=>')) {
                    continue;
                }

                preg_match_all('/\'/', $content, $matches, PREG_OFFSET_CAPTURE);
                $key = substr($content, $matches[0][0][1] + 1, $matches[0][1][1] - $matches[0][0][1] - 1);

                if ($originalArray->has($key)) {
                    continue;
                }

                $modified = true;
                $value = substr($content, $matches[0][2][1] + 1, $matches[0][3][1] - $matches[0][2][1] - 1);
                $content = $languagefile == 'messages.php' ? str_replace($value, $key, $content) : $content;

                $originalFile->splice($number, 0, $content);
            }

            if ($modified) {
                File::put($originalPath, implode($originalFile->toArray()));
            }
        }
    }
}
