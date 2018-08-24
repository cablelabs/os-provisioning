<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Rename Table and Remove unique index on name column so that we can use soft deletes for Authroles
 *
 * @author Nino Ryschawy
 */
class RenameAuthmetaToAuthrole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authmetas', function (Blueprint $table) {
            // $table->dropUnique('authmetas_name_type_unique');
        });

        Schema::rename('authmetas', 'authrole');

        Schema::table('authusermeta', function (Blueprint $table) {
            $table->renameColumn('meta_id', 'role_id');
        });
        Schema::rename('authusermeta', 'authuser_role');

        Schema::table('authmetacore', function (Blueprint $table) {
            $table->renameColumn('meta_id', 'role_id');
        });
        Schema::rename('authmetacore', 'authrole_core');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('authrole', 'authmetas');

        Schema::table('authmetas', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::rename('authuser_role', 'authusermeta');
        Schema::table('authusermeta', function (Blueprint $table) {
            $table->renameColumn('role_id', 'meta_id');
        });
        Schema::rename('authrole_core', 'authmetacore');
        Schema::table('authmetacore', function (Blueprint $table) {
            $table->renameColumn('role_id', 'meta_id');
        });
    }
}
