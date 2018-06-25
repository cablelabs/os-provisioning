# source environment variables to use php 7.1
source scl_source enable rh-php71

dir="/var/www/nmsprime"

cd "$dir"
php artisan module:publish
php artisan module:migrate
php artisan nms:auth

systemctl reload httpd

chown -R apache $dir/storage $dir/bootstrap/cache
chown -R apache:dhcpd /etc/dhcp-nmsprime
