<?php

namespace App\Providers;

use Validator;
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
        $this->app['validator']->extend('ip', 'Acme\Validators\ExtendedValidator@validateIpaddr');
        $this->app['validator']->extend('mac', 'Acme\Validators\ExtendedValidator@validateMac');
        $this->app['validator']->extend('geopos', 'Acme\Validators\ExtendedValidator@validateGeopos');
        $this->app['validator']->extend('docsis', 'Acme\Validators\ExtendedValidator@validateDocsis');
        $this->app['validator']->extend('ip_in_range', 'Acme\Validators\ExtendedValidator@validateIpInRange');
        $this->app['validator']->extend('ip_larger', 'Acme\Validators\ExtendedValidator@ipLarger');
        $this->app['validator']->extend('netmask', 'Acme\Validators\ExtendedValidator@netmask');
        $this->app['validator']->extend('not_null', 'Acme\Validators\ExtendedValidator@notNull');
        $this->app['validator']->extend('null_if', 'Acme\Validators\ExtendedValidator@nullIf');
        $this->app['validator']->extend('creditor_id', 'Acme\Validators\ExtendedValidator@validateCreditorId');
        $this->app['validator']->extend('product', 'Acme\Validators\ExtendedValidator@validateProductType');
        $this->app['validator']->extend('available', 'Acme\Validators\ExtendedValidator@validateBicAvailable');
        $this->app['validator']->extend('phonebook_string', 'Acme\Validators\ExtendedValidator@validatePhonebookString');
        $this->app['validator']->extend('phonebook_predefined_string', 'Acme\Validators\ExtendedValidator@validatePhonebookPredefinedString');
        $this->app['validator']->extend('phonebook_one_character_option', 'Acme\Validators\ExtendedValidator@validatePhonebookOneCharacterOption');

        // the following validators needs to be extended implicit – have to be called even if an empty value is passed
        $this->app['validator']->extendImplicit('phonebook_entry_type_dependend', 'Acme\Validators\ExtendedValidator@validatePhonebookEntryTypeDependend');
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
