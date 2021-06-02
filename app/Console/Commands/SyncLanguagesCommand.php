<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
            $originalArray = collect(require $originalPath);
            $originalFile = collect(file($originalPath));
            $foreign = file(base_path('resources/lang/de/').$languagefile);
            $modified = false;

            foreach ($foreign as $number => $content) {
                if (! \Str::contains($content, '=>')) {
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
