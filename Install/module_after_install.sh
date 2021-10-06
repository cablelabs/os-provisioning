# source environment variables to use php 7.3
module load php80
env='/etc/nmsprime/env'

cd '/var/www/nmsprime'
rm -rf /var/www/nmsprime/bootstrap/cache/*
/opt/remi/php80/root/usr/bin/php artisan module:v6:migrate
/opt/remi/php80/root/usr/bin/php artisan config:cache
/opt/remi/php80/root/usr/bin/php artisan module:publish
/opt/remi/php80/root/usr/bin/php artisan module:migrate
/opt/remi/php80/root/usr/bin/php artisan bouncer:clean
/opt/remi/php80/root/usr/bin/php artisan auth:nms
/opt/remi/php80/root/usr/bin/php artisan route:cache
/opt/remi/php80/root/usr/bin/php artisan view:clear

# on HA machines: clean up
[ -e /var/www/nmsprime/modules/ProvHA/Console/CleanUpSlaveCommand.php ] && /opt/remi/php80/root/usr/bin/php artisan provha:clean_up_slave

# restart laravel background jobs (to make use of new code)
/opt/remi/php80/root/usr/bin/php artisan queue:restart
/opt/remi/php80/root/usr/bin/php artisan websockets:restart

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
