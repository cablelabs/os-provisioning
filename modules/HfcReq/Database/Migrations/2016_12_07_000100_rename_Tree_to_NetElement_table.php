<?php

use Illuminate\Database\Schema\Blueprint;

class RenameTreeToNetElementTable extends BaseMigration
{
    // name of the table to update
    protected $tablename = 'netelement';

    // netelementtypes
    protected $types = ['NET', 'CLUSTER', 'CMTS', 'AMP', 'NODE', 'DATA'];

    /**
     * Run the migrations - Rename tree to netelement and merge both tables
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('device');

        Schema::rename('tree', $this->tablename);

        // add fields to merge tables
        Schema::table('netelement', function (Blueprint $table) {
            $table->integer('netelementtype_id')->unsigned();
            $table->string('community_ro', 45);
            $table->string('community_rw', 45);
            $table->string('address1');
            $table->string('address2');
            $table->string('address3');
            $table->dropColumn('parent_id');
            $table->renameColumn('parent', 'parent_id');
        });

        // adapt existing entries to new table structure
        foreach ($this->types as $key => $type) {
            DB::table($this->tablename)->where('type', '=', $type)->update(['netelementtype_id' => $key + 1]);
        }

        Schema::table('netelement', function (Blueprint $table) {
            $table->dropColumn(['type', 'type_new', 'tp', 'tp_new', 'state', 'state_new']);
        });

        $this->set_fim_fields(['name', 'address1', 'address2', 'address3']);

        return parent::up();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename($this->tablename, 'tree');

        Schema::create('device', function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('devicetype_id')->unsigned();
            $table->string('name');
            $table->string('ip', 15);
            $table->string('community_ro', 45);
            $table->string('community_rw', 45);
            $table->string('address1');
            $table->string('address2');
            $table->string('address3');
            $table->text('description');
        });

        Schema::table('tree', function (Blueprint $table) {
            $table->string('type');
            $table->integer('type_new')->unsigned();
            $table->string('tp', 8);
            $table->integer('tp_new');
            $table->string('state');
            $table->integer('state_new');
            $table->renameColumn('parent_id', 'parent');
            $table->integer('parent_id')->unsigned();
        });

        // This is only safe when we directly rollback the migrations because of error!
        foreach ($this->types as $key => $type) {
            DB::table('tree')->where('netelementtype_id', $key + 1)->update(['type' => $type]);
        }

        Schema::table('tree', function (Blueprint $table) {
            $table->dropColumn(['netelementtype_id', 'community_ro', 'community_rw', 'address1', 'address2', 'address3']);
        });
    }
}
