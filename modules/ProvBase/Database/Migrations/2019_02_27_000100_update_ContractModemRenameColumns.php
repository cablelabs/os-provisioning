<?php

/**
 * Rename network_access to internet_access and telephony_only to has_telephony
 *
 * @author Nino Ryschawy
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContractModemRenameColumns extends Migration
{
    /**
     * Run the migrations.
     * For using the envia TEL API we need some changes in storing the contracts data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->renameColumn('network_access', 'internet_access');
            $table->renameColumn('telephony_only', 'has_telephony');
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->renameColumn('network_access', 'internet_access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->renameColumn('internet_access', 'network_access');
            $table->renameColumn('has_telephony', 'telephony_only');
        });

        Schema::table('modem', function (Blueprint $table) {
            $table->renameColumn('network_access', 'internet_access');
        });
    }
}
