# source environment variables to use php 8.0
source /etc/profile.d/modules.sh
module load php80

#
# variables
#
dir='/var/www/nmsprime'
env='/etc/nmsprime/env'
pw=$(pwgen 12 1) # SQL password for user nmsprime
root_pw=$(pwgen 12 1) # SQL password for root


#
# disable SE linux
#
sed -i "s/^SELINUX=enforcing$/SELINUX=disabled/" /etc/sysconfig/selinux
sed -i "s/^SELINUX=enforcing$/SELINUX=disabled/" /etc/selinux/config
setenforce  0

# set default hostname, if none was explicitly set
if [[ "$(hostname)" == 'localhost.localdomain' ]]; then
	hostnamectl set-hostname nmslx01.nmsprime.test
fi

#
# HTTP
#
# SSL demo certificate
mkdir /etc/httpd/ssl
openssl req -new -x509 -days 3650 -nodes -batch -out /etc/httpd/ssl/httpd.pem -keyout /etc/httpd/ssl/httpd.key
chmod 440 /etc/httpd/ssl/httpd.key
chown root:apache /etc/httpd/ssl/httpd.key

# reload apache config
systemctl start httpd
systemctl enable httpd

# start fpm
systemctl start php80-php-fpm
systemctl enable php80-php-fpm

#
# firewalld
#
# enable admin interface
firewall-cmd --add-port=8080/tcp --zone=public --permanent
firewall-cmd --reload


#
# Postgresql
#
/usr/pgsql-13/bin/postgresql-13-setup initdb
systemctl enable postgresql-13.service
systemctl start postgresql-13.service

# sudo -u postgres /usr/pgsql-13/bin/psql -c 'CREATE database nmsprime' # Is done via dump: sudo -u postgres pg_dump nmsprime -C -x > /tmp/nmsprime.pgsql (-N nmsprime for just dumping schema nmsprime)
sudo -u postgres /usr/pgsql-13/bin/psql -c "CREATE DATABASE nmsprime;"
sudo -u postgres /usr/pgsql-13/bin/psql -c "CREATE USER nmsprime PASSWORD '$pw';"
sudo -u postgres /usr/pgsql-13/bin/psql < /etc/nmsprime/sql-schemas/nmsprime.pgsql
sudo -u postgres /usr/pgsql-13/bin/psql nmsprime -c "
    GRANT ALL ON ALL Tables in schema nmsprime TO nmsprime;
    GRANT ALL ON ALL SEQUENCES IN SCHEMA nmsprime TO nmsprime;
"


#
# mariadb
#
systemctl start mariadb
systemctl enable mariadb

# populate timezone info and set php timezone based on the local one
mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql
zone=$(timedatectl | grep 'Time zone' | cut -d':' -f2 | cut -d' ' -f2)
sed -e "s|^;date.timezone =.*|date.timezone = $zone|" \
    -e 's/^memory_limit =.*/memory_limit = 1024M/' \
    -e 's/^upload_max_filesize =.*/upload_max_filesize = 100M/' \
    -e 's/^post_max_size =.*/post_max_size = 100M/' \
    -i /etc/{,opt/remi/php80/}php.ini

sed -e "s|^#APP_TIMEZONE=|APP_TIMEZONE=$zone|" \
    -e "s/^DB_PASSWORD=$/DB_PASSWORD=$pw/" \
    -i "$env/global.env"

# mysql_secure_installation - necessary for cacti
mysql -u root << EOF
UPDATE mysql.user SET Password=PASSWORD('$root_pw') WHERE User='root';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DELETE FROM mysql.user WHERE User='';
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test_%';
FLUSH PRIVILEGES;
EOF

sed -i "s/^ROOT_DB_PASSWORD=$/ROOT_DB_PASSWORD=$root_pw/" "$env/root.env"

#
# Laravel
#
cd "$dir"

# L5 setup
install -Dm640 -o apache -g root /dev/null /var/www/nmsprime/storage/logs/laravel.log
mkdir -p -m755 "$dir/storage/app/tmp/"
mkdir -p -m755 "$dir/storage/app/public/base/bg-images/"
chown -R apache "$dir/storage/"
rm -rf /var/www/nmsprime/bootstrap/cache/*
/opt/remi/php80/root/usr/bin/php artisan clear-compiled
/opt/remi/php80/root/usr/bin/php artisan optimize
/opt/remi/php80/root/usr/bin/php artisan storage:link

# key:generate needs .env in root dir – create symlink to our env file
ln -srf "$env/global.env" "$dir/.env"
/opt/remi/php80/root/usr/bin/php artisan key:generate
# remove the symlink and create empty .env with comment
rm -f "$dir/.env"
echo "# Use $env/*.env files for configuration" > "$dir/.env"

/opt/remi/php80/root/usr/bin/php artisan migrate
# create default user roles to be later assigned to users
/opt/remi/php80/root/usr/bin/php artisan auth:roles

/opt/remi/php80/root/usr/bin/php artisan config:cache

# Note: needs to run last. storage/logs is only available after artisan optimize
chown -R apache storage bootstrap/cache

# make .env files readable for apache
chgrp -R apache "$env"
chmod 640 "$env"/*.env
# only allow root to read/write mysql root credentials
chown root:root "$env/root.env"
chmod 600 "$env/root.env"

# log
chmod 644 /var/log/messages
systemctl restart rsyslog
systemd-tmpfiles --create

# Supervisord
systemctl enable supervisord
systemctl start supervisord
