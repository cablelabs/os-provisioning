<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateProvmonStoreDnsKey extends BaseMigration
{
    protected $tablename = 'provbase';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // create column to store DNS update key
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->string('dns_password')->after('domain_name');
        });

        $dns_password = 'n/a';
        // get the current DNS password
        if (preg_match('/secret +"?(.*)"?;/', file_get_contents('/etc/named-nmsprime.conf'), $matches)) {
            $dns_password = str_replace('"', '', $matches[1]);
        }

        // or create a new one
        if (
            (substr($dns_password, -1) != '=')
            &&
            (preg_match('/secret "?(.*)"?;/', shell_exec('ddns-confgen -a hmac-md5 -r /dev/urandom | grep secret'), $matches))
        ) {
            $dns_password = str_replace('"', '', $matches[1]);
        }

        // or at least give a hint
        if (substr($dns_password, -1) != '=') {
            $dns_password = 'to be set';
        }

        // store in database
        DB::update("UPDATE $this->tablename SET dns_password='$dns_password'");

        // remove from .env file to avoid confusion
        $conf = file_get_contents('/etc/nmsprime/env/global.env');
        $conf = preg_replace('/\nDNS_PASSWORD=.*$/m', '', $conf);
        file_put_contents('/etc/nmsprime/env/global.env', $conf);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn([
                'dns_password',
            ]);
        });
    }
}
