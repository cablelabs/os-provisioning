<?php

/**
 * This file restarts the dhcpd server after checking if the syntax of the configuration is correct
 */
// int $ret;

echo 'hello';

exec("/usr/sbin/dhcpd -t", $ret);

// var_dump($ret);