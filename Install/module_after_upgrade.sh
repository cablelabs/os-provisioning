# source environment variables to use php 7.1
source scl_source enable rh-php71

dir="/var/www/nmsprime"

cd "$dir"
/opt/rh/rh-php71/root/usr/bin/php artisan module:publish
/opt/rh/rh-php71/root/usr/bin/php artisan module:migrate
/opt/rh/rh-php71/root/usr/bin/php artisan queue:restart
/opt/rh/rh-php71/root/usr/bin/php artisan auth:nms
/opt/rh/rh-php71/root/usr/bin/php artisan route:cache

systemctl reload httpd

chown -R apache $dir/storage $dir/bootstrap/cache
chown -R apache:dhcpd /etc/dhcp-nmsprime
