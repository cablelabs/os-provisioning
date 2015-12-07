<?php
/**
 * Restarts the dhcpd server after checking for correct syntax of the config
 */


// check config
exec("/usr/sbin/dhcpd -t &>/dev/null", $out, $ret);

// restart server
if ($ret == 0)
	exec("systemctl restart dhcpd");
