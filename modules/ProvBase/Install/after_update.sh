# TODO: Remove this code section after NMSPrime v3.2
user=$(php /var/www/nmsprime/artisan tinker --execute "echo DB::connection('pgsql-kea')->getConfig('username');")

sudo -u postgres /usr/pgsql-13/bin/psql -d nmsprime -c "
    GRANT USAGE, CREATE ON SCHEMA public TO ${user};
    GRANT ALL PRIVILEGES ON ALL Tables IN SCHEMA public TO ${user};
    GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO ${user};
"

for tbl in `sudo -u postgres /usr/pgsql-13/bin/psql -qAt -c "select tablename from pg_tables where schemaname = 'public';" kea`;
do sudo -u postgres /usr/pgsql-13/bin/psql -d kea -c "alter table $tbl owner to '${user}'"; done;
# TODO: END Custom NMS Prime 3.2 Code

systemctl daemon-reload
systemctl restart radiusd
