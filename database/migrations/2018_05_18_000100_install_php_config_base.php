<?php

class InstallPhpConfigBase extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (['mysql', 'mysql-ccc'] as $val) {
            $con = DB::connection($val);
            $db = $con->getDatabaseName();
            $char = $con->getConfig('charset');
            $coll = $con->getConfig('collation');
            $user = $con->getConfig('username');
            $pw = $con->getConfig('password');

            // this doesn't work with DB::statement
            system("echo 'ALTER DATABASE $db CHARACTER SET '$char' COLLATE '$coll';' | mysql -u $user -p$pw");
        }

        $tz = date_default_timezone_get();
        foreach (['/etc/php.ini', '/etc/opt/rh/rh-php71/php.ini'] as $file) {
            if (! is_file($file)) {
                continue;
            }
            $str = file_get_contents($file);
            $str = preg_replace('/^;date\.timezone =$/m', "date.timezone = $tz", $str);
            $str = preg_replace('/^memory_limit =.*/m', 'memory_limit = 1024M', $str);
            $str = preg_replace('/^upload_max_filesize =.*/m', 'upload_max_filesize = 50M', $str);
            $str = preg_replace('/^post_max_size =.*/m', 'post_max_size = 50M', $str);
            file_put_contents($file, $str);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
