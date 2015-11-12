<?php

/**
 * This Deamon proofs generically if a file for all specified services exist that indicates that the service has to be restarted
 * @author Nino Ryschawy
 */


/* Don't forget to insert all needed services here! */
$services = array('dhcpd');

// dir with files that indicate that the config of a service has changed - NOTE: How to use app_path() from Laravel ??
$dir = '/var/www/lara/app/storage/systemd/';
// $dir = app_path('storage/systemd');

// dir with the restart scripts
$dir_scripts = '/var/www/lara/app/extensions/systemd/';
// $dir_scripts = app_path('extensions/systemd');

$i = 0;

while (1)
{

	// start from beginning at end of array
	if (!array_key_exists($i, $services))
		$i = 0;

	// proof if indication file exists, if so execute the restart script
	if (file_exists($dir.$services[$i]))
	{
		echo $dir_scripts.$services[$i].'.php';
		if (!file_exists($dir_scripts.$services[$i].'.php'))
		{
			// TODO: print to log file
			echo 'Error: service restart script not found!';
			continue;
		}
		// execute in background
		exec('php -f '.$dir_scripts.$services[$i].'.php &');
		unlink($dir.$services[$i]);
		echo 'file deleted';
	}

	// test output
	echo $i;

	$i++;

	sleep(1);
}