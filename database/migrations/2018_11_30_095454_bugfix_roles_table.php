<?php

use App\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class BugfixRolesTable extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = Role::all();

        foreach ($roles as $role) {
            if ($role->name == 'admin') {
                $role->rank = 101;
                continue;
            }

            if ($role->name == 'support') {
                $role->rank = 100;
                continue;
            }

            if ($role->name == 'guest') {
                $role->rank = 0;
                continue;
            }

            $role->rank = 100 - $role->id;
        }

        Bouncer::allow('guest')->to('view', '*');
        Bouncer::allow('guest')->to('view_analysis_pages_of', \Modules\ProvBase\Entities\Modem::class);
        Bouncer::allow('guest')->to('view_analysis_pages_of', \Modules\ProvBase\Entities\Cmts::class);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            //
        });
    }
}
