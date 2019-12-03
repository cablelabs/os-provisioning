<?php

use Illuminate\Database\Schema\Blueprint;

class InstallInitRadiusAndAcs extends BaseMigration
{
    protected $tablename = 'modem';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            // align with freeradius DB
            $table->string('ppp_username', 64)->nullable();
            // nmsprime default
            $table->string('ppp_password', 191)->nullable();
        });

        // use schema from git, since it adds the id column in radusergroup
        \DB::unprepared(file_get_contents('https://raw.githubusercontent.com/FreeRADIUS/freeradius-server/b838f5178fe092598fb3459dedb5e1ea49b41340/raddb/mods-config/sql/main/mysql/schema.sql'));

        $defReply = new Modules\ProvBase\Entities\RadGroupReply;
        $defReply->groupname = $defReply::$defaultGroup;
        $defReply->attribute = 'Acct-Interim-Interval';
        $defReply->op = ':=';
        $defReply->value = 300;
        $defReply->save();

        // this (Fall-Through) must be the last DB entry of $defaultGroup
        $defReply = new Modules\ProvBase\Entities\RadGroupReply;
        $defReply->groupname = $defReply::$defaultGroup;
        $defReply->attribute = 'Fall-Through';
        $defReply->op = '=';
        $defReply->value = 'Yes';
        $defReply->save();

        $config = DB::connection('mysql-radius')->getConfig();

        $find = [
            '/^\s*#*\s*driver\s*=.*/m',
            '/^\s*#*\s*dialect\s*=.*/m',
            '/^\s*#*\s*login\s*=.*/m',
            '/^\s*#*\s*password\s*=.*/m',
            '/^\s*radius_db\s*=.*/m',
            '/^\s*#*\s*read_clients\s*=.*/m',
        ];

        $replace = [
            "\tdriver = \"rlm_sql_mysql\"",
            "\tdialect = \"mysql\"",
            "\tlogin = \"{$config['username']}\"",
            "\tpassword = \"{$config['password']}\"",
            "\tradius_db = \"{$config['database']}\"",
            "\tread_clients = yes",
        ];

        $filename = '/etc/raddb/mods-available/sql';
        $content = file_get_contents($filename);
        $content = preg_replace($find, $replace, $content);
        file_put_contents($filename, $content);

        $link = '/etc/raddb/mods-enabled/sql';
        symlink('/etc/raddb/mods-available/sql', $link);
        // we can't user php chrgp, since it always dereferences symbolic links
        exec("chgrp -h radiusd $link");

        $observer = new Modules\ProvBase\Entities\QosObserver;
        foreach (Modules\ProvBase\Entities\Qos::all() as $qos) {
            $observer->created($qos);
        }

        $filename = '/lib/node_modules/genieacs/config/config.json';
        $conf = json_decode(file_get_contents($filename));
        // firmware files are hosted on same machine as CWMP
        unset($conf->FS_HOSTNAME);
        // enable TLS for CWMP messages (ACS <-> CPE)
        $conf->CWMP_SSL = true;
        // northbound interface is localhost only, thus we don't need TLS
        $conf->NBI_INTERFACE = 'localhost';
        file_put_contents($filename, json_encode($conf));

        $crt = glob('/var/lib/acme/certs/*.crt');
        if (count($crt) == 1) {
            $crt = reset($crt);
            $link['crt'] = $crt;
            $link['key'] = '/etc/pki/tls/private/'.basename($crt, '.crt').'.key';
        } else {
            $link['crt'] = '/etc/httpd/ssl/httpd.pem';
            $link['key'] = '/etc/httpd/ssl/httpd.key';
        }

        foreach ($link as $ext => $target) {
            exec("ln -srf $target /lib/node_modules/genieacs/config/cwmp.$ext");
        }

        chmod('/lib/node_modules/genieacs/config/cwmp.key', 0444);

        foreach (['radiusd', 'mongod', 'genieacs-cwmp', 'genieacs-fs', 'genieacs-nbi'] as $service) {
            exec("systemctl enable $service.service");
            exec("systemctl start $service.service");
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
