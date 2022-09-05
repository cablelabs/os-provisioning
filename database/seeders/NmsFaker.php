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
