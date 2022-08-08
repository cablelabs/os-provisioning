<?php

// Only restart when file is not currently written
$fp = fopen('/etc/telegraf/telegraf.d/ccap-cli.conf', 'c');
flock($fp, LOCK_EX);

exec('systemctl restart telegraf');

fclose($fp);
