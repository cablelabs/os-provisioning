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

namespace Modules\ProvBase\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateGenieAcsPresetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected $modem, protected $text)
    {
        $this->modem = $modem;
        $this->text = $text;
    }

    /**
     * Create TR-069 configfile.
     * GenieACS API: http://docs.genieacs.com/en/latest/
     *
     * @author Ole Ernst
     *
     * @return void
     */
    public function handle()
    {
        $text = $this->text ?? $this->modem->configfile->text;
        if (! $text) {
            return;
        }

        preg_match('/#SETTINGS:(.+)/', $this->modem->configfile->text, $json);
        $settings = json_decode($json[1] ?? null, true);

        $events = [];
        if (isset($settings['EVENT'])) {
            foreach ($settings['EVENT'] as $value) {
                if (! $value) {
                    continue;
                }

                $events[$value] = true;
            }
        } else {
            $events['0 BOOTSTRAP'] = true;
        }

        $this->createGenieAcsProvisions($text, $events);

        $preset = [
            'weight' => 0,
            'precondition' => "DeviceID.SerialNumber = \"{$this->modem->serial_num}\"",
            'events' => $events,
            'configurations' => [
                [
                    'type' => 'provision',
                    'name' => "prov-{$this->modem->id}",
                    'args' => null,
                ],
            ],
        ];

        $this->modem::callGenieAcsApi("presets/prov-{$this->modem->id}", 'PUT', json_encode($preset));

        // generate monitoring presets...
        $preset['configurations'][0]['name'] = "mon-{$this->modem->configfile->id}";

        foreach (explode(',', config('provbase.cwmpMonitoringEvents')) as $event) {
            if (! isset($this->modem::CWMP_EVENTS[$event])) {
                continue;
            }
            unset($preset['events']);
            $preset['events'][$event.' '.$this->modem::CWMP_EVENTS[$event]] = true;
            $this->modem::callGenieAcsApi("presets/mon-{$this->modem->id}-{$event}", 'PUT', json_encode($preset));
        }
    }

    /**
     * Create Provision from configfile.text.
     *
     * @author Roy Schneider
     *
     * @param  string  $text
     * @param  array  $events
     * @return bool
     */
    protected function createGenieAcsProvisions($text, $events = [])
    {
        $prefix = '';

        // during bootstrap always clear the info we have about the device
        $prov = [];
        if (count($events) == 1 && array_key_exists('0 BOOTSTRAP', $events)) {
            $prov = [
                "clear('Device', Date.now());",
                "clear('InternetGatewayDevice', Date.now());",
            ];
        }

        foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
            $vals = str_getcsv(trim($line), ';');
            if (! count($vals) || ! in_array($vals[0], ['acl', 'add', 'clr', 'commit', 'del', 'get', 'jmp', 'reboot', 'set', 'fw', 'raw'])) {
                continue;
            }

            if (! isset($vals[1])) {
                $vals[1] = '';
            }

            $path = trim("$prefix.$vals[1]", '.');

            switch ($vals[0]) {
                case 'acl':
                    if (isset($vals[1])) {
                        $acl = '';
                        if (! empty($vals[2])) {
                            $acl = "'$vals[2]'";
                        }

                        $prov[] = "declare('$vals[1]', {accessList: Date.now()}, {accessList: [$acl]});";
                    }
                    break;
                case 'add':
                    if (isset($vals[2])) {
                        $prov[] = "declare('$path.[$vals[2]]', {value: Date.now()}, {path: 1});";
                    }
                    break;
                case 'clr':
                    $prov[] = "clear('$path', Date.now());";
                    break;
                case 'commit':
                    $prov[] = 'commit();';
                    break;
                case 'del':
                    $prov[] = "declare('$path.[]', null, {path: 0})";
                    break;
                case 'get':
                    $prov[] = "declare('$path.*', {value: Date.now()});";
                    break;
                case 'jmp':
                    $prefix = trim($vals[1], '.');
                    break;
                case 'reboot':
                    if (! $vals[1]) {
                        $vals[1] = 0;
                    }
                    $prov[] = "declare('Reboot', null, {value: Date.now() - ($vals[1] * 1000)});";
                    break;
                case 'set':
                    if (isset($vals[2])) {
                        $alias = (empty($vals[3]) || empty($vals[4])) ? '' : ".[$vals[3]].$vals[4]";
                        $prov[] = "declare('$path$alias', {value: Date.now()} , {value: '$vals[2]'});";
                    }
                    break;
                case 'fw':
                    if (! empty($vals[1]) && ! empty($vals[2])) {
                        $prov[] = "declare('Downloads.[FileType:$vals[1]]', {path: 1}, {path: 1});";
                        $prov[] = "declare('Downloads.[FileType:$vals[1]].FileName', {value: 1}, {value: '$vals[2]'});";
                        $prov[] = "declare('Downloads.[FileType:$vals[1]].Download', {value: 1}, {value: Date.now()});";
                    }
                    break;
                case 'raw':
                    $prov[] = "$vals[1]";
                    break;
            }
        }

        $this->modem::callGenieAcsApi("provisions/prov-{$this->modem->id}", 'PUT', implode("\n", $prov));
    }
}
