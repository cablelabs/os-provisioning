# source environment variables to use php 7.3
source scl_source enable rh-php73

chown apache:dhcpd /etc/named-ddns.sh
chmod 750 /etc/named-ddns.sh

cd '/var/www/nmsprime'
/opt/rh/rh-php73/root/usr/bin/php artisan nms:radgroupreply-repopulate
