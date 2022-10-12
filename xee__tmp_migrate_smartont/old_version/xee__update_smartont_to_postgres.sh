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
    sudo -u postgres $PSQL -c "DROP DATABASE IF EXISTS $DROPTABLE;"
done
for DROPUSER in $DROPUSERS; do
    sudo -u postgres $PSQL -c "DROP USER IF EXISTS $DROPUSER;"
done

# Just convert nmsprime database to be able to run migrations
# 0 => DB , 1 => PSW, 2 => user
read -r -a auths <<< $(grep '^DB_DATABASE\|^DB_USERNAME\|^DB_PASSWORD' /etc/nmsprime/env/global.env | sort | cut -d'=' -f2 | xargs)

# Avoid pgloader exceptions
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

# create user in maria that is allowed to read for conversion
read -r -a credentials <<< $(grep '^ROOT_DB_USERNAME\|^ROOT_DB_PASSWORD=' /etc/nmsprime/env/root.env | cut -d '=' -f2)
mysql -u "${credentials[0]}" -p"${credentials[1]}" --exec='Create user psqlconverter; GRANT select ON *.* TO psqlconverter;'

# create database and user in postgres
sudo -u postgres $PSQL -c "CREATE DATABASE nmsprime;"
sudo -u postgres $PSQL -d nmsprime -c "
    CREATE USER ${auths[2]} PASSWORD '${auths[1]}';
"

# sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime < /etc/nmsprime/sql-schemas/nmsprime.pgsql
# import schema, index and grant (combined from nino and smartont)
sudo -u postgres /usr/pgsql-13/bin/psql < $MIGRATION_DIR/xee__nmsprime.schema.smartont.pgsql
sudo -u postgres /usr/pgsql-13/bin/psql < $MIGRATION_DIR/xee__nmsprime.index.smartont.pgsql
sudo -u postgres /usr/pgsql-13/bin/psql < $MIGRATION_DIR/xee__nmsprime.grant.smartont.pgsql

# convert data from maria to postgres
echo "LOAD DATABASE
  FROM mysql://psqlconverter@localhost/nmsprime
  INTO postgresql:///nmsprime
  WITH data only, truncate, batch rows = 5000, prefetch rows = 5000
  EXCLUDING TABLE NAMES MATCHING 'hfcbase','nas','radacct','radcheck','radgroupcheck','radgroupreply','radippool','radpostauth','radreply','radusergroup'
    ;" > /tmp/nmsprime.load
sudo -u postgres $PGLOADER -q /tmp/nmsprime.load

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

$PHP /var/www/nmsprime/artisan config:clear
