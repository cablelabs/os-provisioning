<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateConfigfileAddCvc extends Migration
{
    /**
     * Run the migrations.
     * Instead of pasting MfgCVCData into configfile better use certificate files, extracted via:
     * openssl pkcs7 -print_certs -inform DER -in fw.img | openssl x509 -outform DER -out CVC.der
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configfile', function (Blueprint $table) {
            $table->string('cvc')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configfile', function (Blueprint $table) {
            $table->dropColumn(['cvc']);
        });
    }
}
