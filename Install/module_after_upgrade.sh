# source environment variables to use php 8.0
source /etc/profile.d/modules.sh
module load php80

cd '/var/www/nmsprime'

# Run artisan commands only after all installed NMSPrime modules have been upgraded
tmpFile="$(mktemp)"
lastModule=1
rpm -qa nmsprime-* --queryformat '%{NAME}-%{VERSION}-%{RELEASE}\n' | sort > $tmpFile

# Get packages that not have been updated yet
read -r -a packages <<< $(cut -d '-' -f1,2 $tmpFile | uniq -c | grep 1 | cut -d '1' -f2 | sed 's/^ *//')

# Check if all packages have the newest version (also new manually installed packages)
if [ ${#packages[@]} -ne 0 ]; then
    newVersion=$(grep "nmsprime-base" $tmpFile | cut -d'-' -f3 | tail -1)

    for package in ${packages[@]}; do
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
    /opt/remi/php80/root/usr/bin/php artisan module:v6:migrate
    /opt/remi/php80/root/usr/bin/php artisan optimize:clear
    /opt/remi/php80/root/usr/bin/php artisan module:publish
    /opt/remi/php80/root/usr/bin/php artisan migrate
    /opt/remi/php80/root/usr/bin/php artisan module:migrate
    /opt/remi/php80/root/usr/bin/php artisan bouncer:clean
    /opt/remi/php80/root/usr/bin/php artisan auth:nms
    /opt/remi/php80/root/usr/bin/php artisan optimize

    # on HA machines: clean up
    [ -e /var/www/nmsprime/modules/ProvHA/Console/CleanUpSlaveCommand.php ] &&
        /opt/remi/php80/root/usr/bin/php artisan module:list | grep -i provha | grep -i enabled &&
        /opt/remi/php80/root/usr/bin/php artisan provha:clean_up_slave

    # on HA machines: process migrations
    [ -e /var/www/nmsprime/modules/ProvHA/Console/MigrateSlaveCommand.php ] &&
    /opt/remi/php80/root/usr/bin/php artisan module:list | grep -i provha | grep -i enabled &&
    /opt/remi/php80/root/usr/bin/php artisan provha:migrate_slave

    # reread supervisor config and restart affected processes
    /usr/bin/supervisorctl update

    # finally: rebuild dhcpd/named config
    /opt/remi/php80/root/usr/bin/php artisan nms:dhcp
fi

systemctl reload httpd

rm -f $tmpFile
rm -f storage/framework/sessions/*
chown -R apache storage bootstrap/cache /var/log/nmsprime
chown -R apache:dhcpd /etc/dhcp-nmsprime
systemd-tmpfiles --create

sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime -c "
    GRANT SELECT ON ALL TABLES IN SCHEMA nmsprime TO grafana;
    GRANT USAGE ON SCHEMA nmsprime TO grafana;
"
