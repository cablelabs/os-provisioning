# source environment variables to use php 8
source /etc/profile.d/modules.sh
module load php80

# TODO: Remove this code section after NMSPrime v3.2
# The whole code just does the initial steps that need to be done before Laravel can be used again with Pgsql
# these files cause an error on install, hence we have to remove it (would be done anyway during cleanup)
rm -f '/var/www/nmsprime/vendor/symfony/translation/TranslatorInterface.php'

if [ -f "/var/www/nmsprime/modules/HfcCustomer/Config/config.php" ]; then
    echo "<?php return [];" > '/var/www/nmsprime/modules/HfcCustomer/Config/config.php'
fi

if [ -f "/var/www/nmsprime/modules/HfcReq/Config/config.php" ]; then
    echo "<?php return [];" > '/var/www/nmsprime/modules/HfcReq/Config/config.php'
    sed -i "0,/NetElement.searchNetsClusters/s//NetElement.searchClusters/" '/var/www/nmsprime/modules/HfcReq/Http/routes.php'
fi

if [ -f "/var/www/nmsprime/modules/HfcSnmp/Config/config.php" ]; then
    echo "<?php return [];" > '/var/www/nmsprime/modules/HfcSnmp/Config/config.php'
fi

if [ -f "/var/www/nmsprime/modules/Ccc/Http/routes.php" ]; then
    truncate -s0 '/var/www/nmsprime/modules/Ccc/Http/routes.php'
fi


if [ -f "/var/www/nmsprime/modules/ProvMon/Http/routes.php" ]; then
    truncate -s0 '/var/www/nmsprime/modules/ProvMon/Http/routes.php'
fi

if [ -f "/var/www/nmsprime/modules/ProvVoip/Config/config.php" ]; then
    echo "<?php return [];" > '/var/www/nmsprime/modules/ProvVoip/Config/config.php'
fi

if [ -f "/var/www/nmsprime/modules/PropertyManagement/Config/config.php" ]; then
    echo "<?php return [];" > '/var/www/nmsprime/modules/PropertyManagement/Config/config.php'
fi

read -r -a auths <<< $(grep '^DB_DATABASE\|^DB_USERNAME\|^DB_PASSWORD' /etc/nmsprime/env/global.env | sort | cut -d'=' -f2 | xargs)

# this file is not present before upgrade, hence we have to do it here...
sudo -u postgres psql nmsprime < /etc/nmsprime/sql-schemas/nmsprime.pgsql

# Remove default entries from schema
sudo -u postgres /usr/pgsql-13/bin/psql nmsprime -c '
    Delete from nmsprime.abilities;
    Delete from nmsprime.carriercode;
    Delete from nmsprime.configfile;
    Delete from nmsprime.costcenter;
    Delete from nmsprime.billingbase;
    Delete from nmsprime.ccc;
    Delete from nmsprime.company;
    Delete from nmsprime.contract;
    Delete from nmsprime.ekpcode;
    Delete from nmsprime.global_config;
    Delete from nmsprime.hfcreq;
    Delete from nmsprime.overduedebts;
    Delete from nmsprime.phonetariff;
    Delete from nmsprime.provbase;
    Delete from nmsprime.provvoip;
    Delete from nmsprime.roles;
    Delete from nmsprime.sepaaccount;
    Delete from nmsprime.ticketsystem;
    Delete from nmsprime.trcclass;
    Delete from nmsprime.netelementtype;
    Delete from nmsprime.provmon;
    Delete from nmsprime.qos;
    Delete from nmsprime.sla;
'

echo "LOAD DATABASE
  FROM mysql://psqlconverter@localhost/nmsprime
  INTO postgresql:///nmsprime
  WITH data only, reset sequences, truncate, batch rows = 5000, prefetch rows = 5000
  EXCLUDING TABLE NAMES MATCHING 'hfcbase','nas','radacct','radcheck','radgroupcheck','radgroupreply','radippool','radpostauth','radreply','radusergroup'
    ;" > /tmp/nmsprime.load

sudo -u postgres pgloader -q /tmp/nmsprime.load

sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime -c "
    GRANT USAGE, CREATE ON SCHEMA ${auths[0]} TO ${auths[2]};
    GRANT ALL PRIVILEGES ON ALL Tables in schema ${auths[0]} TO ${auths[2]};
    GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA ${auths[0]} TO ${auths[2]};
"

laravelModules=$(php /var/www/nmsprime/artisan module:list | cut -d'|' -f2)
if echo "$laravelModules" | grep -q "ProvMon"; then
    sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime -c "
        GRANT SELECT ON ALL TABLES IN SCHEMA ${auths[0]} TO grafana;
        GRANT USAGE ON SCHEMA ${auths[0]} TO grafana;
    "
fi

sed -i 's/^#RADIUS_DB/RADIUS_DB/' /etc/nmsprime/env/provbase.env
sed -i "s/^RADIUS_DB_PASSWORD=.*$/RADIUS_DB_PASSWORD=$(pwgen 12 1)/" /etc/nmsprime/env/provbase.env

if [ -d "/var/www/nmsprime/resources/lang" ]; then
    rm -rf "/var/www/nmsprime/resources/lang"
fi
# TODO: END Custom NMS Prime 3.2 Code

cd '/var/www/nmsprime'

systemctl restart httpd php80-php-fpm nmsprimed

rm -f storage/framework/sessions/*
chown -R apache storage bootstrap/cache

systemd-tmpfiles --create

/opt/remi/php80/root/usr/bin/php artisan clear-compiled
/opt/remi/php80/root/usr/bin/php artisan optimize:clear
/opt/remi/php80/root/usr/bin/php artisan optimize
/opt/remi/php80/root/usr/bin/php artisan migrate
