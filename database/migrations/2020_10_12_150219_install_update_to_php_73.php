<?php

use Illuminate\Support\Facades\Artisan;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Illuminate\Database\Migrations\Migration;

class InstallUpdateToPhp73 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        system('systemctl stop rh-php71-php-fpm.service');
        system('systemctl disable rh-php71-php-fpm.service');

        system('systemctl start rh-php73-php-fpm.service');
        system('systemctl enable rh-php73-php-fpm.service');

        Artisan::call('module:v6:migrate');
        Bouncer::refresh();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        system('systemctl stop rh-php73-php-fpm.service');
        system('systemctl disable rh-php73-php-fpm.service');

        system('systemctl start rh-php71-php-fpm.service');
        system('systemctl enable rh-php71-php-fpm.service');

        unlink('/var/www/nmsprime/modules_statuses.json');
    }
}
