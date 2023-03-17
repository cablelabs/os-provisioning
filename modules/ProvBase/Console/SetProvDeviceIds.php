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

namespace Modules\ProvBase\Console;

use Illuminate\Console\Command;
use Modules\HfcReq\Entities\NetElement;
use Modules\ProvBase\Entities\NetGw;

class SetProvDeviceIds extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nms:setProvDeviceIds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

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
        $netElements = NetElement::where(fn ($query) => $query->whereNull('prov_device_id')->orWhere('prov_device_id', '0'))
            ->join('netelementtype', 'netelementtype.id', '=', 'netelement.netelementtype_id')
            ->whereIn('base_type_id', [2, 3, 4, 5])
            ->without('netelementtype')
            ->select('netelement.name', 'netelementtype.base_type_id', 'netelement.id', 'netelement.ip')
            ->get();
        $netGws = NetGw::select('id', 'hostname')->without(['ippools', 'netelement'])->get();
        $netGwCount = $netElements->where('base_type_id', 3)->count();

        foreach ($netElements->where('base_type_id', 3) as $netElement) {
            foreach ($netGws as $netGw) {
                if ($netGw->hostname == $netElement->name) {
                    $netElement->prov_device_id = $netGw->id;
                    $this->info("Connecting NetGW with hostname {$netGw->hostname} (id: {$netGw->id}) to NetElement with Name {$netElement->name} (id: {$netElement->id}).");
                    $netElement->save() ? $this->info('success', 'v') : $this->error('Failed to update NetElement!');
                    $netGwCount--;
                }
            }
        }
        $this->info("There are {$netGwCount} NetGws left without a prov_device_id");

        $netElements = $netElements->filter(fn ($netElement) => $netElement->base_type_id != 3 && $netElement->ip);

        foreach ($netElements as $netElement) {
            if ($id = $netElement->getModemIdFromHostname()) {
                $this->info("Connecting cm-{$id} to NetElement with Name {$netElement->name} (id: {$netElement->id}).");
                $netElement->prov_device_id = $id;
                $netElement->save() ? $this->info('success', 'v') : $this->error('Failed to update NetElement!');
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
