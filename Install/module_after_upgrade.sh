# source environment variables to use php 7.3
source scl_source enable rh-php73

cd '/var/www/nmsprime'

# Run artisan commands only after all installed NMSPrime modules have been upgraded
tmpFile='/tmp/nmsprime-packages'$(date +%s)
lastModule=1
rpm -qa nmsprime-* --queryformat '%{NAME}-%{VERSION}-%{RELEASE}\n' | sort > $tmpFile

# Get packages that not have been updated yet
packages=$(cut -d '-' -f1,2 $tmpFile | uniq -c | grep 1 | cut -d '1' -f2 | sed 's/^ *//')

# Check if all packages have the newest version (also new manually installed packages)
if [[ $packages ]]; then
    newVersion=$(grep "nmsprime-base" $tmpFile | cut -d'-' -f3 | tail -1)

    for package in $packages; do
    packageVersion=$(grep $package $tmpFile | cut -d'-' -f3 | tail -1)
    # echo "$package - new Version: $newVersion - package Version: $packageVersion"

        if [ "$packageVersion" != "$newVersion" ]; then
            lastModule=0
        fi
    done
fi

# Migrate when all modules are upgraded
if [ $lastModule -eq 1 ]; then
    rm -f /var/www/nmsprime/config/excel.php
    rm -rf /var/www/nmsprime/bootstrap/cache/*
    /opt/rh/rh-php73/root/usr/bin/php artisan config:cache
    /opt/rh/rh-php73/root/usr/bin/php artisan clear-compiled
    /opt/rh/rh-php73/root/usr/bin/php artisan optimize
    /opt/rh/rh-php73/root/usr/bin/php artisan migrate
    /opt/rh/rh-php73/root/usr/bin/php artisan module:migrate
    /opt/rh/rh-php73/root/usr/bin/php artisan module:publish
    #/opt/rh/rh-php73/root/usr/bin/php artisan queue:restart
    pkill -f "artisan queue:work"
    /opt/rh/rh-php73/root/usr/bin/php artisan bouncer:clean
    /opt/rh/rh-php73/root/usr/bin/php artisan auth:nms
    /opt/rh/rh-php73/root/usr/bin/php artisan route:cache
    /opt/rh/rh-php73/root/usr/bin/php artisan view:clear
fi

systemctl reload httpd

rm -f $tmpFile
rm -f storage/framework/sessions/*
chown -R apache storage bootstrap/cache /var/log/nmsprime
chown -R apache:dhcpd /etc/dhcp-nmsprime
systemd-tmpfiles --create
