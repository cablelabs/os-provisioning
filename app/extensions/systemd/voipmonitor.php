<?php

$path = '/var/www/lara/storage/systemd/voipmonitor';

if(!file_exists($path))
	return;

$cmd = file($path, FILE_IGNORE_NEW_LINES)[0];
exec("systemctl $cmd voipmonitor.service");
// delete file to signify that we are done
unlink($path);
