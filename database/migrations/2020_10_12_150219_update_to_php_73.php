<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;

class UpdateToPhp73 extends Migration
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

        system('rm /var/www/nmsprime/modules_statuses.json');
    }
}
