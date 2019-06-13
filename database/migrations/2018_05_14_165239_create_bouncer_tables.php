<?php

use App\Role;
use App\User;
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
                $table->string('description')->nullable();
                $table->integer('rank')->unsigned();
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

            $users = User::all();
            foreach ($users as $user) {
                $roles = \DB::table('authuser_role')
                        ->join('authrole', 'authrole.id', '=', 'authuser_role.role_id')
                        ->join('users', 'users.id', '=', 'authuser_role.user_id')
                        ->select('authrole.*')
                        ->where('users.id', '=', $user->id)->get();
                foreach ($roles as $role) {
                    if ($user->id == 1) {
                        $role->name = ($role->name == 'super_admin') ? 'admin' : $role->name;
                    } else {
                        $role->name = ($role->name == 'super_admin') ? 'support' : $role->name;
                    }

                    if (! Role::find($role->id)) {
                        Role::create([
                            'id' => $role->id,
                            'name' => ! (Role::where('name', $role->name)->count()) ? $role->name : $role->name.$role->id,
                            'title' => Str::studly($role->name),
                            'rank' => 101 - $role->id,
                            'description' => $role->description,
                        ]);
                    }
                    Bouncer::assign($role->name)->to($user);
                    Bouncer::allow($user)->toOwn(User::class); // Ability to manage own Usermodel
                }
            }
            //create Custom Abilities
            Bouncer::allow('admin')->everything();
            Bouncer::allow('support')->everything();
            Bouncer::allow('guest')->to('view', '*'); //role for demo system or presentation
            Bouncer::forbid('support')->to('use api');
            Bouncer::forbid('support')->to('see income chart');
            Bouncer::forbid('support')->toManage(Role::class);

            \Artisan::call('auth:nms');

            $admin = Role::where('name', 'admin')->first();
            $admin->rank = 101;
            $admin->save();
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
        Schema::table('guilog', function (Blueprint $table) {
            $table->renameColumn('user_id', 'authuser_id');
        });

        Schema::rename('users', 'authusers');

        Schema::create('authrole', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('name', 191);
            $table->enum('type', ['role', 'client']);
            $table->string('description');

            $table->unique(['name', 'type']);
        });

        Schema::create('authcores', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name', 191);
            $table->enum('type', ['model', 'net']);
            $table->string('description');
            $table->unique(['name', 'type']);
        });

        Schema::create('authuser_role', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('role_id')->unsigned()->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('authusers');
            $table->foreign('role_id')
                ->references('id')
                ->on('authrole');

            $table->unique(['user_id', 'role_id']);
        });

        Schema::create('authrole_core', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('role_id')->unsigned()->nullable();
            $table->integer('core_id')->unsigned()->nullable();
            $table->boolean('view')->default(0);
            $table->boolean('create')->default(0);
            $table->boolean('edit')->default(0);
            $table->boolean('delete')->default(0);

            $table->foreign('role_id')
                ->references('id')
                ->on('authrole');
            $table->foreign('core_id')
                ->references('id')
                ->on('authcores');

            $table->unique(['role_id', 'core_id']);
        });

        // the following “seeding” is needed in every case – even if the seeders will not be run!
        // add superuser
        if (empty(DB::select('SELECT * FROM authusers where id = 1'))) {
            DB::table('authusers')->insert([
                'id' => 1,
                'first_name' => 'superuser',
                'last_name' => 'initial',
                'email' => 'root@localhost',
                'login_name' => 'root',
                'password' => \Hash::make('toor'),
                'description' => 'Superuser to do base config. Initial password is “toor” – change this ASAP or delete this user!!',
            ]);
        }

        DB::table('authrole')->insert([
            ['id' => 1, 'name'=>'super_admin', 'type'=>'role', 'description'=>'Is allowed to do everything. Used for the initial user which can add other users.'],
            ['id' => 2, 'name'=>'every_net', 'type'=>'client', 'description'=>'Is allowed to access every net. Used for the initial user which can add other users.'],
        ]);

        DB::update('INSERT INTO authuser_role (user_id, role_id) VALUES(1, 1);');
        DB::update('INSERT INTO authuser_role (user_id, role_id) VALUES(1, 2);');

        // add each existing model
        require_once getcwd().'/app/BaseModel.php';
        foreach (BaseModel::get_models() as $model) {
            DB::table('authcores')->insert(['name'=>$model, 'type'=>'model']);
        }

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

        Schema::dropIfExists(Models::table('permissions'));
        Schema::dropIfExists(Models::table('assigned_roles'));
        Schema::dropIfExists(Models::table('roles'));
        Schema::dropIfExists(Models::table('abilities'));
    }
}
