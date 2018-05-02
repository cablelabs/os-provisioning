<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAuthcoresToPermissions extends Migration
{
    protected $tablename = 'permissions';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('authcores');

        Schema::create($this->tablename, function (Blueprint $table) {
            $table->increments('id');
            $table->enum('action', ['create', 'read', 'update', 'delete']);
            $table->string('name');
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();
        });

        require_once(getcwd()."/app/Models/BaseModel.php");
		foreach(BaseModel::get_models() as $model) {
            $modelPathArray = explode('\\',$model);
            $modelname = str_plural(array_pop($modelPathArray));
			DB::table($this->tablename)->insert(['action'=> 'create', 'name' => $model, 'description' => 'Can create '. $modelname]);
			DB::table($this->tablename)->insert(['action'=> 'read', 'name' => $model, 'description' => 'Can read '.$modelname]);
			DB::table($this->tablename)->insert(['action'=> 'update', 'name' => $model, 'description' => 'Can update '.$modelname]);
			DB::table($this->tablename)->insert(['action'=> 'delete', 'name' => $model, 'description' => 'Can delete '.$modelname]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists($this->tablename);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        Schema::create('authcores', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name', 191);
            $table->enum('type', array('model', 'net'));
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(array('name', 'type'));
        });

        require_once(getcwd()."/app/Models/BaseModel.php");
        foreach(BaseModel::get_models() as $model) {
            DB::table('authcores')->insert(['name'=>$model, 'type' => 'model']);
        }

    }
}
