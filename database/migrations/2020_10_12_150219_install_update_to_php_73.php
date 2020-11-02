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

        $zone = exec("timedatectl | grep 'Time zone' | cut -d':' -f2 | cut -d' ' -f2");
        exec("sed -e 's|^;date.timezone =.*|date.timezone = {$zone}|' -e 's/^memory_limit =.*/memory_limit = 1024M/' -e 's/^upload_max_filesize =.*/upload_max_filesize = 100M/' -e 's/^post_max_size =.*/post_max_size = 100M/' -i /etc/opt/rh/rh-php73/php.ini");

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
