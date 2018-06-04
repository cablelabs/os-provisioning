<?php

use Silber\Bouncer\Database\Models;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBouncerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::rename('authusers', 'users');
            Schema::table('guilog', function (Blueprint $table) {
                $table->renameColumn('authuser_id', 'user_id');
            });
            DB::statement('ALTER TABLE `authrole` ENGINE=InnoDB;');
            DB::statement('ALTER TABLE `users` ENGINE=InnoDB;');
        });

        DB::transaction(function () {
            Schema::create(Models::table('abilities'), function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 150);
                $table->string('title')->nullable();
                $table->integer('entity_id')->unsigned()->nullable();
                $table->string('entity_type', 150)->nullable();
                $table->boolean('only_owned')->default(false);
                $table->integer('scope')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create(Models::table('roles'), function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 150);
                $table->string('title')->nullable();
                $table->integer('level')->unsigned()->nullable();
                $table->integer('scope')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(
                    ['name', 'scope'],
                    'roles_name_unique'
                );
            });

            Schema::create(Models::table('assigned_roles'), function (Blueprint $table) {
                $table->integer('role_id')->unsigned()->index();
                $table->integer('entity_id')->unsigned();
                $table->string('entity_type', 150);
                $table->integer('scope')->nullable()->index();
                $table->timestamps();

                $table->index(
                    ['entity_id', 'entity_type', 'scope'],
                    'assigned_roles_entity_index'
                );

                $table->foreign('role_id')
                      ->references('id')->on(Models::table('roles'))
                      ->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::create(Models::table('permissions'), function (Blueprint $table) {
                $table->integer('ability_id')->unsigned()->index();
                $table->integer('entity_id')->unsigned();
                $table->string('entity_type', 150);
                $table->boolean('forbidden')->default(false);
                $table->integer('scope')->nullable()->index();
                $table->timestamps();

                $table->index(
                    ['entity_id', 'entity_type', 'scope'],
                    'permissions_entity_index'
                );

                $table->foreign('ability_id')
                      ->references('id')->on(Models::table('abilities'))
                      ->onUpdate('cascade')->onDelete('cascade');
            });

            $users = \App\User::all();
            foreach ($users as $user) {
                $roles = \DB::table('authuser_role')
                        ->join('authrole', 'authrole.id', '=', 'authuser_role.role_id')
                        ->join('users', 'users.id', '=', 'authuser_role.user_id')
                        ->select('authrole.name')
                        ->where('users.id', '=', $user->id)->get();
                foreach ($roles as $role) {
                    Bouncer::assign($role->name)->to($user);
                }
            }
            Bouncer::allow('super-admin')->everything();

        });

        Schema::dropIfExists('authrole');
        Schema::dropIfExists('authcores');
        Schema::dropIfExists('authuser_role');
        Schema::dropIfExists('authrole_core');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('users', 'authusers');
        Schema::table('guilog', function (Blueprint $table) {
            $table->renameColumn('user_id', 'authuser_id');
        });
        //Schema::drop('roles');

        Schema::create('authrole', function(Blueprint $table) {

            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('name', 191);
            $table->enum('type', array('role', 'client'));
            $table->string('description');

            $table->unique(array('name', 'type'));
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!
        DB::table('authrole')->insert([
            ['id' => 1, 'name'=>'super_admin', 'type'=>'role', 'description'=>'Is allowed to do everything. Used for the initial user which can add other users.'],
            ['id' => 2, 'name'=>'every_net', 'type'=>'client', 'description'=>'Is allowed to access every net. Used for the initial user which can add other users.'],
        ]);

        Schema::create('authcores', function(Blueprint $table) {

            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name', 191);
            $table->enum('type', array('model', 'net'));
            $table->string('description');

            $table->unique(array('name', 'type'));
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!
        // add each existing model
        require_once(getcwd()."/app/BaseModel.php");
        foreach(BaseModel::get_models() as $model) {
            DB::table('authcores')->insert(['name'=>$model, 'type'=>'model']);
        }

        Schema::create('authuser_role', function(Blueprint $table) {

            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('authuser');
            $table->foreign('role_id')->references('id')->on('authrole');

            $table->unique(array('user_id', 'role_id'));
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!
        DB::update("INSERT INTO ".'authuser_role'." (user_id, role_id) VALUES(1, 1);");
        DB::update("INSERT INTO ".'authuser_role'." (user_id, role_id) VALUES(1, 2);");

        Schema::create('authrole_core', function(Blueprint $table) {

            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('role_id')->unsigned();
            $table->integer('core_id')->unsigned();
            $table->boolean('view')->default(0);
            $table->boolean('create')->default(0);
            $table->boolean('edit')->default(0);
            $table->boolean('delete')->default(0);

            $table->foreign('role_id')->references('id')->on('authrole');
            $table->foreign('core_id')->references('id')->on('authcore');

            $table->unique(array('role_id', 'core_id'));
        });


        // the following “seeding” is needed in every case – even if the seeders will not be run!

        // add relations meta<->core for role super_admin
        $models = DB::table('authcores')->select('id')->where('type', 'LIKE', 'model')->get();
        foreach ($models as $model) {
            DB::table('authrole_core')->insert([
                'role_id' => 1,
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
            DB::table('authrole_core')->insert([
                'role_id' => 2,
                'core_id' => $net->id,
                'view' => 1,
                'create' => 1,
                'edit' => 1,
                'delete' => 1,
            ]);
        }

        Schema::drop(Models::table('permissions'));
        Schema::drop(Models::table('assigned_roles'));
        Schema::drop(Models::table('roles'));
        Schema::drop(Models::table('abilities'));
    }
}
