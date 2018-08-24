<?php

use Illuminate\Database\Schema\Blueprint;

class CreateAuthcoreTable extends BaseMigration
{
    // name of the table to create
    protected $tablename = 'authcores';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $this->up_table_generic($table);

            $table->string('name', 191);
            $table->enum('type', ['model', 'net']);
            $table->string('description');

            $table->unique(['name', 'type']);
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!
        // add each existing model
        require_once getcwd().'/app/BaseModel.php';
        foreach (BaseModel::get_models() as $model) {
            DB::table($this->tablename)->insert(['name'=>$model, 'type'=>'model']);
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
