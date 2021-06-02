<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
