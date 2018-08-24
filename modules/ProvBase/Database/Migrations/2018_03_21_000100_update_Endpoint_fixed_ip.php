<?php

use Illuminate\Database\Schema\Blueprint;

class UpdateEndpointFixedIp extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'endpoint';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->integer('modem_id')->unsigned()->nullable();
            $table->string('ip', 15)->nullable();
            $table->dropColumn('type');
            $table->dropColumn('name');
        });

        DB::statement("ALTER TABLE $this->tablename MODIFY hostname varchar(63)");
        DB::statement("ALTER TABLE $this->tablename CHANGE public fixed_ip tinyint(1)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tablename, function (Blueprint $table) {
            $table->dropColumn(['modem_id', 'ip']);
            $table->enum('type', ['cpe', 'mta'])->nullable();
            $table->string('name')->nullable();
        });

        DB::statement("ALTER TABLE $this->tablename MODIFY hostname varchar(255)");
        DB::statement("ALTER TABLE $this->tablename CHANGE fixed_ip public tinyint(1)");
    }
}
