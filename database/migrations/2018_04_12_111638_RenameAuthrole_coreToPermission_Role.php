<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAuthroleCoreToPermissionRole extends Migration
{
    protected $tablename = 'permission_role';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('authrole_core');

        Schema::create($this->tablename, function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade');

            $table->foreign('permission_id')
                  ->references('id')
                  ->on('permissions')
                  ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->primary(['permission_id', 'role_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tablename);

        Schema::create('authrole_core', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->integer('core_id');
            $table->integer('view');
            $table->integer('create');
            $table->integer('edit');
            $table->integer('delete');
            $table->timestamps();
        });

        require_once(getcwd()."/app/Models/BaseModel.php");
        foreach(BaseModel::get_models() as $model) {
            DB::table('authrole_core')->insert([
                'role_id' => 1,
                'core_id' => 5,
                'view'    => 1,
                'create'  => 1,
                'edit'    => 1,
                'delete'  => 1,
                ]);
        }
    }
}
