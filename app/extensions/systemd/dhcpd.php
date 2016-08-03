<?php
/**
 * Restarts the dhcpd server after checking for correct syntax of the config
 */

$path = '/var/www/lara/storage/systemd/dhcpd';

// check config
exec("/usr/sbin/dhcpd -t &>/dev/null", $out, $ret);

// restart server
if ($ret == 0)
	exec("systemctl restart dhcpd");

// delete file to signify that we are done
unlink($path);
