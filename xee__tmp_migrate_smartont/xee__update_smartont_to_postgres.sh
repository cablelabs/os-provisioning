#!/bin/bash

echo
echo
echo "########################################################################################################################################################################################################"
date -Iseconds
echo "Executing $0"
echo "################################################################################"
echo
echo
echo

LOOKFOR="DB_CONNECTION="
grep -q $LOOKFOR /etc/nmsprime/env/global.env
if [ $? -gt 0 ]; then
    echo "“$LOOKFOR” not in /etc/nmsprime/env/global.env"
    echo "Add and try again"
    exit 1
fi

PGLOADER="/usr/bin/pgloader"
PHP="/opt/remi/php80/root/usr/bin/php"
PSQL="/usr/pgsql-13/bin/psql"
PG_DUMP="/usr/pgsql-13/bin/pg_dump"

NMSDIR="/var/www/nmsprime"
SCHEMA_DUMP_FILE="/tmp/nmsprime.schema.pgsql"
MIGRATION_DIR="/var/www/nmsprime/xee__tmp_migrate_smartont"

yum install -y freeradius-postgresql freeradius-utils

cp -f $NMSDIR"/modules/HfcBase/Install/files/hfcbase.env" /etc/nmsprime/env/hfcbase.env
chown root:apache /etc/nmsprime/env/hfcbase.env
chmod 640 /etc/nmsprime/env/hfcbase.env

systemctl restart postgresql-13.service

DROPTABLES="
nmsprime
nmsprime_ccc
radius
kea
"
DROPUSERS="
nmsprime
nmsprime_ccc
radius
kea
"

for DROPTABLE in $DROPTABLES; do
    echo ">>> Dropping databases…"
    sudo -u postgres $PSQL -c "DROP DATABASE IF EXISTS $DROPTABLE;"
done
for DROPUSER in $DROPUSERS; do
    echo ">>> Dropping users…"
    sudo -u postgres $PSQL -c "DROP USER IF EXISTS $DROPUSER;"
done

# Just convert nmsprime database to be able to run migrations
# 0 => DB , 1 => PSW, 2 => user
echo ">>> Reading DB credentials for user nmsprime…"
read -r -a auths <<< $(grep '^DB_DATABASE\|^DB_USERNAME\|^DB_PASSWORD' /etc/nmsprime/env/global.env | sort | cut -d'=' -f2 | xargs)

# Avoid pgloader exceptions
echo ">>> Preparing mariadb DB to avoid pgloader exceptions…"
mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
    UPDATE configfile set device = 'cm' where device is NULL or device = '';
    UPDATE contract set birthday = NULL where birthday = '0000-00-00';
    UPDATE item set valid_from = NULL where valid_from = '0000-00-00';
    UPDATE item set valid_to = NULL where valid_to = '0000-00-00';
    UPDATE mta set type = 'sip' where type is NULL or type = '';
    UPDATE oid set type = 'u' where type = '' or type is null;
    UPDATE phonetariff set voip_protocol = 'SIP' where voip_protocol = '' or voip_protocol is null;
    UPDATE sepamandate set valid_to = NULL where valid_to = '0000-00-00';
    ALTER TABLE billingbase modify rcd smallint(6) null;
    ALTER TABLE billingbase modify cdr_offset smallint(6) null;
    ALTER TABLE debt modify indicator smallint(6) null;
    ALTER TABLE item modify payed_month smallint(6) null;
    ALTER TABLE settlementrun modify month smallint(6) null;
    ALTER TABLE invoice modify month smallint(6) null;
    ALTER TABLE costcenter modify billing_month smallint(6) null;
    ALTER TABLE mpr drop column type;
    ALTER TABLE mpr drop column prio;
    ALTER TABLE mprgeopos drop column name;
    ALTER TABLE mprgeopos drop column description;
    ALTER TABLE netelement drop column id_name;
"

# The following lines are specific for SmartOnt module conversions
# e.g. there are some migrations that won't run in mariadb (but are already part of psql schema)
echo ">>> Executing SmartOnt specific mariadb commands…"
mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
    INSERT INTO migrations (migration, batch) VALUES ('2021_09_20_000100_switchMysqlToPgsql', 22);
    INSERT INTO migrations (migration, batch) VALUES ('2022_07_08_082200_create_alarm_table', 22);
    INSERT INTO migrations (migration, batch) VALUES ('2022_07_19_000100_create_scan_range', 22);
    INSERT INTO migrations (migration, batch) VALUES ('2022_08_04_000100_change_ip_pool_type_convert_enum_add_vendor_class_identifier', 22);
    ALTER TABLE ippool ADD COLUMN vendor_class_identifier VARCHAR(191) DEFAULT NULL;
    INSERT INTO migrations (migration, batch) VALUES ('2022_11_12_095943_add_cpe_device_count_to_ccap', 22);

    /* # the following is coming from database/migrations/2021_09_20_000100_switchMysqlToPgsql.php */
    DELETE FROM ippool WHERE netmask='' OR ip_pool_start='' OR ip_pool_end='' OR router_ip='';
    /* there are no ippools at smartont instances; it is save to just drop the column */
    /* ALTER table ippool */
    /*     ALTER COLUMN net type inet USING net::inet, */
    /*     ALTER COLUMN ip_pool_start type inet USING ip_pool_start::inet, */
    /*     ALTER COLUMN ip_pool_end type inet USING ip_pool_end::inet, */
    /*     ALTER COLUMN router_ip type inet USING router_ip::inet; */
    ALTER TABLE ippool DROP COLUMN netmask;
"


# run the working migrations
echo ">>> Executing missing migrations in mariadb…"
sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=mysql/" /etc/nmsprime/env/global.env
$PHP /var/www/nmsprime/artisan optimize:clear
cd /var/www/nmsprime
php artisan migrate
php artisan module:migrate

# create user in maria that is allowed to read for conversion
echo ">>> Reading mariadb credentials for user root…"
read -r -a credentials <<< $(grep '^ROOT_DB_USERNAME\|^ROOT_DB_PASSWORD=' /etc/nmsprime/env/root.env | cut -d '=' -f2)
echo ">>> Creating mariadb user psqlconverter…"
mysql -u "${credentials[0]}" -p"${credentials[1]}" --exec='Create user psqlconverter; GRANT select ON *.* TO psqlconverter;'

# create database and user in postgres
echo ">>> Creating postgres database nmsprime…"
sudo -u postgres $PSQL -c "CREATE DATABASE nmsprime;"
echo ">>> Creating postgres user nmsprime…"
sudo -u postgres $PSQL -d nmsprime -c "
    CREATE USER ${auths[2]} PASSWORD '${auths[1]}';
"

# sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime < /etc/nmsprime/sql-schemas/nmsprime.pgsql
# import schema, index and grant (combined from nino and smartont)
# sudo -u postgres /usr/pgsql-13/bin/psql < $MIGRATION_DIR/xee__nmsprime.schema.smartont.pgsql
# sudo -u postgres /usr/pgsql-13/bin/psql < $MIGRATION_DIR/xee__nmsprime.index.smartont.pgsql
# sudo -u postgres /usr/pgsql-13/bin/psql < $MIGRATION_DIR/xee__nmsprime.grant.smartont.pgsql
sudo -u postgres /usr/pgsql-13/bin/psql < $MIGRATION_DIR/xee__nmsprime.current_empty_db_dump.pg.sql     # this is the empty current postgres schema; maybe we need to dump it again!
# sudo -u postgres /usr/pgsql-13/bin/pg_restore -Fc -d nmsprime -n nmsprime --clean --if-exists < /tmp/nmsprime_dump.sql
# sudo -u postgres /usr/pgsql-13/bin/pg_restore -Fc -d nmsprime -n nmsprime < /tmp/nmsprime_dump.sql



# convert data from maria to postgres
echo ">>> Loading database to file…"
echo "LOAD DATABASE
  FROM mysql://psqlconverter@localhost/nmsprime
  INTO postgresql:///nmsprime
  WITH data only, truncate, batch rows = 5000, prefetch rows = 5000
  EXCLUDING TABLE NAMES MATCHING 'hfcbase','nas','radacct','radcheck','radgroupcheck','radgroupreply','radippool','radpostauth','radreply','radusergroup','migrations','dpic_card'
    ;" > /tmp/nmsprime.load
echo ">>> Executing pgloader…"
sudo -u postgres $PGLOADER -q /tmp/nmsprime.load
exit
sudo -u postgres $PSQL -d nmsprime -c "
    GRANT USAGE, CREATE ON SCHEMA ${auths[0]} TO ${auths[2]};
    GRANT ALL PRIVILEGES ON ALL Tables in schema ${auths[0]} TO ${auths[2]};
    GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA ${auths[0]} TO ${auths[2]};
    GRANT SELECT ON ALL TABLES IN SCHEMA ${auths[0]} TO grafana;
    GRANT USAGE ON SCHEMA ${auths[0]} TO grafana;
"

# sudo -u postgres $PG_DUMP -s -d nmsprime > $SCHEMA_DUMP_FILE

# sed -i 's/ OWNER TO postgres;/ OWNER TO nmsprime;/g' $SCHEMA_DUMP_FILE
# sed -i 's/Owner: postgres/Owner: nmsprime/g' $SCHEMA_DUMP_FILE
# sed -i 's/ NOT NULL//g' $SCHEMA_DUMP_FILE
# sed -i 's/ id bigint/ id bigint NOT NULL/g' $SCHEMA_DUMP_FILE
# sed -i "s/language character varying(191) DEFAULT 'de'::character varying,/language character varying(191) DEFAULT 'en'::character varying,/g" $SCHEMA_DUMP_FILE
# sed -i 's/house_number character varying(8),/house_number character varying(20),/g' $SCHEMA_DUMP_FILE
# sed -i 's/ ability_id bigint,/  ability_id bigint NOT NULL,/g' $SCHEMA_DUMP_FILE
# sed -i "s/ apartment_id bigint$/ apartment_id bigint,\\n    id_name character varying GENERATED ALWAYS AS (\\nCASE\\n    WHEN (name IS NULL) THEN ((id)::character varying)::text\\n    WHEN (id IS NULL) THEN (name)::text\\n    ELSE (((id)::character varying)::text || '_'::text) || ((name)::text)\\nEND) STORED/g" $SCHEMA_DUMP_FILE
# sed -i 's///g' $SCHEMA_DUMP_FILE

sed -i 's/^#RADIUS_DB/RADIUS_DB/' /etc/nmsprime/env/provbase.env
sed -i "s/^RADIUS_DB_PASSWORD=.*$/RADIUS_DB_PASSWORD=$(pwgen 12 1)/" /etc/nmsprime/env/provbase.env

sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=pgsql/" /etc/nmsprime/env/global.env
$PHP /var/www/nmsprime/artisan optimize:clear

$PHP /var/www/nmsprime/artisan config:clear
