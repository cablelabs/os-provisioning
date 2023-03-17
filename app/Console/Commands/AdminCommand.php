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

use App\Role;
use App\User;
use Bouncer;
use Illuminate\Console\Command;

class AdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:admin {name : The login name of the User.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make a User Admin.';

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
        Bouncer::allow('admin')->everything();
        Bouncer::unforbid('admin')->everything();

        $name = trim($this->input->getArgument('name'));

        $user = User::where('login_name', $name)->first();

        if ($user == null) {
            return $this->error('User not found. Please check if u provided the correct login name!');
        }

        Role::pluck('name')->each(function ($role) use ($user) {
            Bouncer::retract($role)->from($user);
        });

        Bouncer::assign('admin')->to($user);
        Bouncer::refreshFor($user);

        $this->info('Role Admin assigned to '.$user->login_name);
    }
}
