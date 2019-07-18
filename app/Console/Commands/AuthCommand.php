<?php

namespace App\Console\Commands;

use Bouncer;
use App\Role;
use App\User;
use App\Ability;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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
    protected static function customAbilities() : Collection
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
            [
                'name' => 'view_analysis_pages_of',
                'title' => 'View analysis pages of modems',
                'entity_type' => \Modules\ProvBase\Entities\Modem::class,
                'only_owned'  =>'0',
            ],
            [
                'name' => 'view_analysis_pages_of',
                'title' => 'View analysis pages of cmts',
                'entity_type' => \Modules\ProvBase\Entities\Cmts::class,
                'only_owned'  =>'0',
            ],
            [
                'name' => 'download',
                'title' => 'Download settlement runs',
                'entity_type' => \Modules\BillingBase\Entities\SettlementRun::class,
                'only_owned' =>'0',
            ],
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() : void
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
    protected function resetAdminRole() : void
    {
        $this->setVerbosity('v');

        Bouncer::allow('admin')->everything();
        Bouncer::unforbid('admin')->everything();

        $this->line('Admin Role reset.');

        $this->setVerbosity('normal');
    }

    /**
     * Give each User ownage over his own User Model for Usermanagement.
     *
     * @return void
     */
    protected function resetUserPermissions() : void
    {
        $this->setVerbosity('vv');

        foreach (User::all() as $user) {
            Bouncer::allow($user)->toOwn(User::class);
            $this->line($user->login_name.' was given Permission to edit its own Model.');
        }

        $this->setVerbosity('v');

        $this->line('User Permissions reset.');

        $this->setVerbosity('normal');
    }

    /**
     * Create all Custom abilities, if they are deleted or modified by accident
     *
     * @return void
     */
    protected function resetCustomAbilities() : void
    {
        $this->setVerbosity('vv');

        foreach (self::customAbilities() as $ability) {
            $ability = Ability::firstOrCreate($ability);

            $this->line("Ability {$ability->title} processed. It has id {$ability->id}");
        }

        $this->setVerbosity('v');

        $this->line('Custom Abilities reset.');

        $this->setVerbosity('normal');
    }
}
