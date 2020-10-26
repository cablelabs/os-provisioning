# source environment variables to use php 7.3
source scl_source enable rh-php73
env='/etc/nmsprime/env'

cd '/var/www/nmsprime'
rm -rf /var/www/nmsprime/bootstrap/cache/*
/opt/rh/rh-php73/root/usr/bin/php artisan config:cache
/opt/rh/rh-php73/root/usr/bin/php artisan module:publish
/opt/rh/rh-php73/root/usr/bin/php artisan module:migrate
#/opt/rh/rh-php73/root/usr/bin/php artisan queue:restart
pkill -f "artisan queue:work"
/opt/rh/rh-php73/root/usr/bin/php artisan bouncer:clean
/opt/rh/rh-php73/root/usr/bin/php artisan auth:nms
/opt/rh/rh-php73/root/usr/bin/php artisan route:cache
/opt/rh/rh-php73/root/usr/bin/php artisan view:clear

systemctl reload httpd

chown -R apache storage bootstrap/cache /var/log/nmsprime
chown -R apache:dhcpd /etc/dhcp-nmsprime
systemd-tmpfiles --create

# make .env files readable for apache
chgrp -R apache "$env"
chmod 640 "$env"/*.env
# only allow root to read/write mysql root credentials
chown root:root "$env/root.env"
chmod 600 "$env/root.env"
