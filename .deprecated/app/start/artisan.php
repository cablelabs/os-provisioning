<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/


Artisan::add(new cactiCommand);


/*
 * TODO: This is only a workaround and should be moved to Module context
 */

$m = new \BaseModel;

if (\PPModule::is_active('ProvBase'))
{
	Artisan::add(new Modules\ProvBase\Console\dhcpCommand);
	Artisan::add(new Modules\ProvBase\Console\configfileCommand);
}

if (\PPModule::is_active('HfcBase'))
{
	Artisan::add(new Modules\HfcBase\Console\TreeBuildCommand);
}