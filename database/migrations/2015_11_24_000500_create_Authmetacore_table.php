<?php

use Illuminate\Database\Schema\Blueprint;

class CreateAuthmetacoreTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'authmetacore';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->integer('meta_id')->unsigned();
            $table->integer('core_id')->unsigned();
            $table->boolean('view')->default(0);
            $table->boolean('create')->default(0);
            $table->boolean('edit')->default(0);
            $table->boolean('delete')->default(0);

            $table->foreign('meta_id')->references('id')->on('authmeta');
            $table->foreign('core_id')->references('id')->on('authcore');

            $table->unique(['meta_id', 'core_id']);
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!

        // add relations meta<->core for role super_admin
        $models = DB::table('authcores')->select('id')->where('type', 'LIKE', 'model')->get();
        foreach ($models as $model) {
            DB::table($this->tablename)->insert([
                'meta_id' => 1,
                'core_id' => $model->id,
                'view' => 1,
                'create' => 1,
                'edit' => 1,
                'delete' => 1,
            ]);
        }

        // add relations meta<->core for client every_net
        $nets = DB::table('authcores')->select('id')->where('type', 'LIKE', 'net')->get();
        foreach ($nets as $net) {
            DB::table($this->tablename)->insert([
                'meta_id' => 2,
                'core_id' => $net->id,
                'view' => 1,
                'create' => 1,
                'edit' => 1,
                'delete' => 1,
            ]);
        }
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
