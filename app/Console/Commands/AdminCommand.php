<?php

namespace App\Console\Commands;

use Bouncer;
use App\Role;
use App\User;
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
    public function fire()
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
