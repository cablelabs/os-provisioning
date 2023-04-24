# source environment variables to use php 8.0
source /etc/profile.d/modules.sh
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
[ -e /var/www/nmsprime/modules/ProvHA/Console/CleanUpSlaveCommand.php ] &&
	/opt/remi/php80/root/usr/bin/php artisan module:list | grep -i provha | grep -i enabled &&
	/opt/remi/php80/root/usr/bin/php artisan provha:clean_up_slave

# reread supervisor config and restart affected processes
/usr/bin/supervisorctl update

systemctl reload httpd

chown -R apache storage bootstrap/cache
systemd-tmpfiles --create

# make .env files readable for apache
chgrp -R apache "$env"
chmod 640 "$env"/*.env
# only allow root to read/write mysql root credentials
chown root:root "$env/root.env"
chmod 600 "$env/root.env"
