<?php

use Illuminate\Database\Schema\Blueprint;

class CreateProvBaseTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'provbase';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('provisioning_server');
            $table->string('ro_community');
            $table->string('rw_community');
            $table->string('notif_mail');
            $table->string('domain_name');
            $table->integer('dhcp_def_lease_time')->unsigned();
            $table->integer('dhcp_max_lease_time')->unsigned();
            $table->integer('startid_contract')->unsigned();
            $table->integer('startid_modem')->unsigned();
            $table->integer('startid_endpoint')->unsigned();
        });

        DB::update('INSERT INTO '.$this->tablename." (provisioning_server, ro_community, rw_community, domain_name, dhcp_def_lease_time, dhcp_max_lease_time) VALUES('172.20.0.1', 'public', 'private', 'nmsprime.test', 86400, 172800);");
        // create dhcpd config files
        exec('php /var/www/nmsprime/artisan nms:dhcp');
        exec('chown -R apache:dhcpd /etc/dhcp-nmsprime');

        return parent::up();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->tablename);
    }
}
