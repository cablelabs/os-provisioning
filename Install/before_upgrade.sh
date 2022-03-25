# TODO: Remove this whole code after NMSPrime v3.2
# The whole code just does the initial steps that need to be done before Laravel can be used again with Pgsql

ret=$(sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime -c "SELECT EXISTS(SELECT 1 FROM information_schema.schemata WHERE schema_name = 'nmsprime')")
exists=$(echo $ret | cut -d ' ' -f 3)

if [ exists = 't' ]; then
    exit
fi

# Just convert nmsprime database to be able to run migrations
# 0 => DB , 1 => PSW, 2 => user
read -r -a auths <<< $(grep '^DB_DATABASE\|^DB_USERNAME\|^DB_PASSWORD' /etc/nmsprime/env/global.env | sort | cut -d'=' -f2 | xargs)

# Avoid pgloader exceptions
mysql -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" --exec="
    UPDATE contract set birthday = NULL where birthday = '0000-00-00';
    UPDATE item set valid_from = NULL where valid_from = '0000-00-00';
    UPDATE item set valid_to = NULL where valid_to = '0000-00-00';
    UPDATE oid set type = 'u' where type = '' or type is null;
    UPDATE phonetariff set voip_protocol = 'SIP' where voip_protocol = '' or voip_protocol is null;
    ALTER TABLE settlementrun modify month smallint(6) null;
    ALTER TABLE invoice modify month smallint(6) null;
    ALTER TABLE costcenter modify billing_month smallint(6) null;
"

read -r -a credentials <<< $(grep '^ROOT_DB_USERNAME\|^ROOT_DB_PASSWORD=' /etc/nmsprime/env/root.env | cut -d '=' -f2)
mysql -u "${credentials[0]}" -p"${credentials[1]}" --exec='Create user psqlconverter; GRANT select ON *.* TO psqlconverter;'

sudo -u postgres pgloader mysql://psqlconverter@localhost/nmsprime postgresql:///nmsprime

sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime -c "
    CREATE USER ${auths[2]} PASSWORD '${auths[1]}';
    GRANT USAGE ON SCHEMA ${auths[0]} TO ${auths[2]};
    GRANT ALL PRIVILEGES ON ALL Tables in schema ${auths[0]} TO ${auths[2]};
    GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA ${auths[0]} TO ${auths[2]};
"

/opt/remi/php80/root/usr/bin/php /var/www/nmsprime/artisan config:cache
