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

use App\Ability;
use App\User;
use Bouncer;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Nwidart\Modules\Facades\Module;

class AuthCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'auth:nms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Authentication and Access Permissions';

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
     * Holds the Custom Abilities, which should be reset
     *
     * @return Collection
     */
    protected static function customAbilities(): Collection
    {
        return collect([
            [
                'name' => 'use api',
                'title' => 'Use api',
                'only_owned' => '0',
            ],
            [
                'name' => 'see income chart',
                'title' => 'See income chart',
                'only_owned' => '0',
            ],
        ])->when(Module::collections()->has('ProvBase'), function ($collection) {
            return $collection->concat([
                [
                    'name' => 'view_analysis_pages_of',
                    'title' => 'View analysis pages of modems',
                    'entity_type' => \Modules\ProvBase\Entities\Modem::class,
                    'only_owned'  =>'0',
                ],
                [
                    'name' => 'view_analysis_pages_of',
                    'title' => 'View analysis pages of netgw',
                    'entity_type' => \Modules\ProvBase\Entities\NetGw::class,
                    'only_owned'  =>'0',
                ],
            ]);
        })->when(Module::collections()->has('BillingBase'), function ($collection) {
            return $collection->concat([
                [
                    'name' => 'download',
                    'title' => 'Download settlement runs',
                    'entity_type' => \Modules\BillingBase\Entities\SettlementRun::class,
                    'only_owned' =>'0',
                ],
            ]);
        });
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->resetAdminRole();

        $this->resetUserPermissions();

        $this->resetCustomAbilities();

        $this->info('Successfully reset Authentification');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // array('example', InputArgument::REQUIRED, 'An example argument.'),
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
            // array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        ];
    }

    /**
     * Reset the Admin Role.
     *
     * @return void
     */
    protected function resetAdminRole(): void
    {
        Bouncer::allow('admin')->everything();
        Bouncer::unforbid('admin')->everything();

        $this->info('Admin Role reset.', 'v');
    }

    /**
     * Give each User ownage over his own User Model for Usermanagement.
     *
     * @return void
     */
    protected function resetUserPermissions(): void
    {
        foreach (User::all() as $user) {
            Bouncer::allow($user)->toOwn(User::class);
            $this->comment($user->login_name.' was given Permission to edit its own Model.', 'vv');
        }

        $this->info('User Permissions reset.', 'v');
    }

    /**
     * Create all Custom abilities, if they are deleted or modified by accident
     *
     * @return void
     */
    protected function resetCustomAbilities(): void
    {
        foreach (self::customAbilities() as $ability) {
            $ability = Ability::firstOrCreate($ability);
            $this->comment("Ability {$ability->title} processed. It has id {$ability->id}", 'vv');
        }

        $this->info('Custom Abilities reset.', 'v');
    }
}
