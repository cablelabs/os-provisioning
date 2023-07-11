# TODO: Remove this whole code after NMSPrime v3.2
# The whole code just does the initial steps that need to be done before Laravel can be used again with Pgsql
# source environment variables to use php 8
source /etc/profile.d/modules.sh
module load php80

#
# Postgresql
#
/usr/pgsql-13/bin/postgresql-13-setup initdb
systemctl enable postgresql-13.service
systemctl start postgresql-13.service

if [ ! "$(sudo -u postgres /usr/pgsql-13/bin/psql -XtAc "SELECT 1 FROM pg_database WHERE datname='nmsprime'" )" = '1' ]; then
    sudo -u postgres /usr/pgsql-13/bin/psql -c 'create database nmsprime;'
fi

ret=$(sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime -c "SELECT EXISTS(SELECT 1 FROM information_schema.schemata WHERE schema_name = 'nmsprime')")
exists=$(echo $ret | cut -d ' ' -f 3)

if [ exists = 't' ]; then
    echo 'ERROR: nmsprime database already exists. Exit'

    exit
fi

# Just convert nmsprime database to be able to run migrations
# 0 => DB , 1 => PSW, 2 => user
read -r -a auths <<< $(grep '^DB_DATABASE\|^DB_USERNAME\|^DB_PASSWORD' /etc/nmsprime/env/global.env | sort | cut -d'=' -f2 | xargs)

# Avoid pgloader exceptions
laravelModules=$(php /var/www/nmsprime/artisan module:list | cut -d'|' -f2)

if echo "$laravelModules" | grep -q "ProvBase"; then
    mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
        UPDATE configfile set device = 'cm' where device is NULL or device = '';
        UPDATE contract set birthday = NULL where birthday = '0000-00-00';
    "
fi

if echo "$laravelModules" | grep -q "BillingBase"; then
    mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
        UPDATE item set valid_from = NULL where valid_from = '0000-00-00';
        UPDATE item set valid_to = NULL where valid_to = '0000-00-00';
        UPDATE sepamandate set valid_to = NULL where valid_to = '0000-00-00';
        ALTER TABLE billingbase modify rcd smallint(6) null;
        ALTER TABLE billingbase modify cdr_offset smallint(6) null;
        ALTER TABLE item modify payed_month smallint(6) null;
        ALTER TABLE settlementrun modify month smallint(6) null;
        ALTER TABLE invoice modify month smallint(6) null;
        ALTER TABLE costcenter modify billing_month smallint(6) null;
    "
fi

if echo "$laravelModules" | grep -q "OverdueDebts"; then
    mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
        ALTER TABLE debt modify indicator smallint(6) null;
    "
fi

if echo "$laravelModules" | grep -q "ProvVoip"; then
    mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
        UPDATE mta set type = 'sip' where type is NULL or type = '';
        UPDATE phonetariff set voip_protocol = 'SIP' where voip_protocol = '' or voip_protocol is null;
    "
fi

if echo "$laravelModules" | grep -q "HfcReq"; then
    mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
        ALTER TABLE netelement drop column id_name;
    "
fi

if echo "$laravelModules" | grep -q "HfcCustomer"; then
    mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
        ALTER TABLE mpr drop column type;
        ALTER TABLE mpr drop column prio;
        ALTER TABLE mprgeopos drop column name;
        ALTER TABLE mprgeopos drop column description;
    "
fi

if echo "$laravelModules" | grep -q "HfcSnmp"; then
    mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
        UPDATE oid set type = 'u' where type = '' or type is null;
    "
fi

read -r -a credentials <<< $(grep '^ROOT_DB_USERNAME\|^ROOT_DB_PASSWORD=' /etc/nmsprime/env/root.env | cut -d '=' -f2)
mysql -u "${credentials[0]}" -p"${credentials[1]}" --exec='Create user psqlconverter; GRANT select ON *.* TO psqlconverter;'

sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime -c "
    CREATE USER ${auths[2]} PASSWORD '${auths[1]}';
"

php /var/www/nmsprime/artisan config:cache
