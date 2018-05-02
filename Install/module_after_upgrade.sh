# source environment variables to use php 7.1
source scl_source enable rh-php71

dir="/var/www/nmsprime"

cd "$dir"
php artisan module:publish
php artisan module:migrate
php artisan nms:auth

chown -R apache $dir/storage $dir/bootstrap/cache
chown apache /etc/dhcp
chown -R apache:dhcpd /etc/dhcp/nmsprime
