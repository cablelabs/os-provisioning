# source environment variables to use php 7.1
source scl_source enable rh-php71

#
# variables
#
dir="/var/www/nmsprime"
env="/etc/nmsprime/env/global.env"
pw=$(pwgen 12 1) # SQL password for user nmsprime


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
openssl req -new -x509 -days 365 -nodes -batch -out /etc/httpd/ssl/httpd.pem -keyout /etc/httpd/ssl/httpd.key

# reload apache config
systemctl start httpd
systemctl enable httpd

# start fpm
systemctl start rh-php71-php-fpm
systemctl enable rh-php71-php-fpm

#
# firewalld
#
# enable admin interface
firewall-cmd --add-port=8080/tcp --zone=public --permanent
firewall-cmd --reload


#
# mariadb
#
systemctl start mariadb
systemctl enable mariadb

# populate timezone info and set php timezone based on the local one
mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql
zone=$(timedatectl | grep 'Time zone' | cut -d':' -f2 | cut -d' ' -f2)
sed -e "s|^;date.timezone =$|date.timezone = $zone|" \
    -e 's/^memory_limit =.*/memory_limit = 1024M/' \
    -e 's/^upload_max_filesize =.*/upload_max_filesize = 50M/' \
    -e 's/^post_max_size =.*/post_max_size = 50M/' \
    -i /etc/{,opt/rh/rh-php71/}php.ini
sed -i "s|^#APP_TIMEZONE=|APP_TIMEZONE=$zone|" /etc/nmsprime/env/global.env

# create mysql db
mysql -u root -e "CREATE DATABASE nmsprime CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';"

mysql -u root -e "GRANT ALL ON nmsprime.* TO 'nmsprime'@'localhost' IDENTIFIED BY '$pw'";
sed -i "s/^DB_PASSWORD=$/DB_PASSWORD=$pw/" "$env"


#
# Laravel
#
cd "$dir"

# L5 setup
install -Dm640 -o apache -g root /dev/null /var/www/nmsprime/storage/logs/laravel.log
chown apache /var/www/nmsprime/storage/logs/laravel.log
/opt/rh/rh-php71/root/usr/bin/php artisan clear-compiled
/opt/rh/rh-php71/root/usr/bin/php artisan optimize

# key:generate needs .env in root dir – create symlink to our env file
ln -srf /etc/nmsprime/env/global.env "$dir/.env"
/opt/rh/rh-php71/root/usr/bin/php artisan key:generate
# remove the symlink and create empty .env with comment
rm -f "$dir/.env"
echo "# Use /etc/nmsprime/env/*.env files for configuration" > "$dir/.env"

/opt/rh/rh-php71/root/usr/bin/php artisan migrate
# create default user roles to be later assigned to users
/opt/rh/rh-php71/root/usr/bin/php artisan auth:roles

# Note: needs to run last. storage/logs is only available after artisan optimize
chown -R apache storage bootstrap/cache /var/log/nmsprime

# make .env files readable for apache
chgrp -R apache /etc/nmsprime/env
chmod -R o-rwx /etc/nmsprime/env
chmod -R g-w /etc/nmsprime/env

# log
chmod 644 /var/log/messages
systemctl restart rsyslog
