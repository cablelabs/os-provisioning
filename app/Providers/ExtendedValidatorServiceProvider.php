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

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ExtendedValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any necessary services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * Extend Validator Class
         * see extensions/validators/..
         */
        $this->app['validator']->extend('available', 'App\extensions\validators\ExtendedValidator@validateBicAvailable');
        $this->app['validator']->extend('creditor_id', 'App\extensions\validators\ExtendedValidator@validateCreditorId');
        $this->app['validator']->extend('docsis', 'App\extensions\validators\ExtendedValidator@validateDocsis');
        $this->app['validator']->extend('geopos', 'App\extensions\validators\ExtendedValidator@validateGeopos');
        $this->app['validator']->extend('comma_separated_hostnames_or_ips', 'App\extensions\validators\ExtendedValidator@validateCommaSeparatedHostnamesOrIps');
        $this->app['validator']->extend('hostname_or_ip', 'App\extensions\validators\ExtendedValidator@validateHostnameOrIp');
        $this->app['validator']->extend('ip_in_range', 'App\extensions\validators\ExtendedValidator@validateIpInRange');
        $this->app['validator']->extend('ip_larger', 'App\extensions\validators\ExtendedValidator@ipLarger');
        $this->app['validator']->extend('mac', 'App\extensions\validators\ExtendedValidator@validateMac');
        $this->app['validator']->extend('net', 'App\extensions\validators\ExtendedValidator@validateNet');
        $this->app['validator']->extend('not_null', 'App\extensions\validators\ExtendedValidator@notNull');
        $this->app['validator']->extend('null_if', 'App\extensions\validators\ExtendedValidator@nullIf');
        $this->app['validator']->extend('phonebook_one_character_option', 'App\extensions\validators\ExtendedValidator@validatePhonebookOneCharacterOption');
        $this->app['validator']->extend('phonebook_predefined_string', 'App\extensions\validators\ExtendedValidator@validatePhonebookPredefinedString');
        $this->app['validator']->extend('phonebook_string', 'App\extensions\validators\ExtendedValidator@validatePhonebookString');
        $this->app['validator']->extend('product', 'App\extensions\validators\ExtendedValidator@validateProductType');
        $this->app['validator']->extend('template_type_unique', 'App\extensions\validators\ExtendedValidator@validateDocumentTemplateTypeIsUnique');

        $this->app['validator']->extend('empty', 'App\extensions\validators\ExtendedValidator@validateEmpty');

        // the following validators needs to be extended implicit – have to be called even if an empty value is passed
        $this->app['validator']->extendImplicit('phonebook_entry_type_dependend', 'App\extensions\validators\ExtendedValidator@validatePhonebookEntryTypeDependend');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
