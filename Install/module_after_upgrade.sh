# source environment variables to use php 7.1
source scl_source enable rh-php71

cd '/var/www/nmsprime'

# run artisan commands only after the last module has been upgraded
# TODO: if a new nmsprime- package is installed as a dependency
#       the migration may not run for the last, but next to last package
if [ $(rpm -qa nmsprime-* --queryformat '%{VERSION}-%{RELEASE}\n' | sort | uniq -c | awk '{print $1}' | sort -u | wc -l) -eq 1 ]; then
  rm -f /var/www/nmsprime/config/excel.php
  rm -rf /var/www/nmsprime/bootstrap/cache/*
  /opt/rh/rh-php71/root/usr/bin/php artisan config:cache
  /opt/rh/rh-php71/root/usr/bin/php artisan clear-compiled
  /opt/rh/rh-php71/root/usr/bin/php artisan optimize
  /opt/rh/rh-php71/root/usr/bin/php artisan migrate
  /opt/rh/rh-php71/root/usr/bin/php artisan module:migrate
  /opt/rh/rh-php71/root/usr/bin/php artisan module:publish
  #/opt/rh/rh-php71/root/usr/bin/php artisan queue:restart
  pkill -f "artisan queue:work"
  /opt/rh/rh-php71/root/usr/bin/php artisan bouncer:clean
  /opt/rh/rh-php71/root/usr/bin/php artisan auth:nms
  /opt/rh/rh-php71/root/usr/bin/php artisan route:cache
  /opt/rh/rh-php71/root/usr/bin/php artisan view:clear
fi

systemctl reload httpd

rm -f storage/framework/sessions/*
chown -R apache storage bootstrap/cache /var/log/nmsprime
chown -R apache:dhcpd /etc/dhcp-nmsprime
systemd-tmpfiles --create
