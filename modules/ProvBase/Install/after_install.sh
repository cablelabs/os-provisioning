dir="/var/www/nmsprime"
pw=$(openssl rand -base64 16)

# create folders
install -dm750 /etc/dhcp/nmsprime/cmts_gws
install -dm700 /tftpboot/{,cm}
install -dm750 -g named /var/named-nmsprime

# change owner
chown -R apache:dhcpd /etc/dhcp/nmsprime
chown -R apache /tftpboot

sed -i "s|<DNS-PASSWORD>|$pw|" /etc/dhcp/nmsprime/dhcpd.conf
sed -i "s|<DNS-PASSWORD>|$pw|" /etc/named-nmsprime.conf

cd "$dir"
php artisan module:publish
php artisan module:migrate
