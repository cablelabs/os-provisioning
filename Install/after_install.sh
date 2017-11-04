#
# variables
#
dir="/var/www/nmsprime"
env="/etc/nmsprime/env/global.env"
pw=$(tr -dc '[:alnum:]' < /dev/urandom | head -c 12) # SQL password for user nmsprime


#
# disable SE linux
#
sed -i "s/^SELINUX=enforcing$/SELINUX=disabled/" /etc/sysconfig/selinux
setenforce  0


#
# HTTP
#
# SSL demo certificate
mkdir /etc/httpd/ssl
openssl req -new -x509 -days 365 -nodes -batch -out /etc/httpd/ssl/httpd.pem -keyout /etc/httpd/ssl/httpd.key

# reload apache config
systemctl start httpd
systemctl enable httpd


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

# create mysql db
mysql -u root -e "CREATE DATABASE nmsprime;"

mysql -u root -e "GRANT ALL ON nmsprime.* TO 'nmsprime'@'localhost' IDENTIFIED BY '$pw'";
sed -i "s/^DB_PASSWORD=$/DB_PASSWORD=$pw/" "$env"


#
# Laravel
#
cd "$dir"

# L5 setup
php artisan clear-compiled
php artisan optimize

# key:generate needs .env in root dir – create symlink to our env file
ln -s /etc/nmsprime/env/global.env "$dir/.env"
php artisan key:generate
# remove the symlink and create empty .env with comment
rm -f "$dir/.env"
echo "# Use /etc/nmsprime/env/*.env files for configuration" > "$dir/.env"

php artisan migrate

# Note: needs to run last. storage/logs is only available after artisan optimize
chown -R apache $dir/storage/ $dir/bootstrap/cache/

# make .env files readable for apache
chgrp -R apache /etc/nmsprime/env
chmod -R o-rwx /etc/nmsprime/env
chmod -R g-w /etc/nmsprime/env
