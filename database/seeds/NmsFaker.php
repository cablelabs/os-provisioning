<?php

use Faker\Factory as Faker;

/**
 * To prevent seeders and unit testing to create multiple fakers in static
 * context (using ~15MB RAM each) we wrap this in our own faker class.
 * Implemented as singleton following http://www.phptherightway.com/pages/Design-Patterns.html
 */
class NmsFaker extends Faker
{
    // the reference to the singleton object
    private static $instance = null;

    /**
     * Constructor.
     * Declared private to disable creation of GuiLogWriter objects using the “new” keyword
     */
    private function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     */
    private function __wakeup()
    {
    }

    /**
     * Getter for the Faker “object“
     */
    public static function &getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = Faker::create();
        }

        return static::$instance;
    }
}
