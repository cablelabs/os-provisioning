<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreeTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'tree';

    /**
     * Run the migrations - NOTE: This table will be renamed to NetElement in next Migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('name');
            $table->integer('series');
            $table->integer('options');
            $table->string('ip');
            $table->string('pos', 45);
            $table->string('link');
            $table->integer('parent_id')->unsigned();
            $table->integer('user');
            $table->integer('access');
            $table->integer('net');				// for fast assignment of Clusters to Net (it's possible to have multiple parents between cluster & net)
            $table->integer('cluster');
            $table->integer('layer');
            $table->text('descr');
            $table->string('kml_file');
            $table->string('draw');
            $table->string('line');

            // droped on next migration - but here for backward compatibility
            $table->string('type');
            $table->integer('type_new')->unsigned();
            $table->string('tp', 8);
            $table->integer('tp_new');
            $table->string('state');
            $table->integer('state_new');
            $table->integer('parent');
        });

        // TODO: should this be moved to seeding ?
        foreach ([1 => '-unknown parent-', 2 => '-root-'] as $i => $v) {
            DB::update('INSERT INTO '.$this->tablename." (id,name,parent_id,pos) VALUES($i, '$v', 0, '0,0');");
        }

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
