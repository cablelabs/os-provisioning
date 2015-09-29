<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

    /*
     * get local settings partly from /.env.local.php
     */
	'connections' => array(

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => $_ENV['dbHostFromDotenv'],
			'database'  => $_ENV['dbDatabaseFromDotenv'],
			'username'  => $_ENV['dbUsernameFromDotenv'],
			'password'  => $_ENV['dbPasswordFromDotenv'],
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),

	),

);
