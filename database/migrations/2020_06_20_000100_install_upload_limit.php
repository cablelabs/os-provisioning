<?php

class InstallUploadLimit extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        exec("sed -e 's/^upload_max_filesize =.*/upload_max_filesize = 100M/' -e 's/^post_max_size =.*/post_max_size = 100M/' -i /etc/{,opt/rh/rh-php73/}php.ini");
        exec('systemctl restart rh-php73-php-fpm.service');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        exec("sed -e 's/^upload_max_filesize =.*/upload_max_filesize = 50M/' -e 's/^post_max_size =.*/post_max_size = 50M/' -i /etc/{,opt/rh/rh-php73/}php.ini");
        exec('systemctl restart rh-php73-php-fpm.service');
    }
}
