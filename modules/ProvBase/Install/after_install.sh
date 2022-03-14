dir='/var/www/nmsprime'
env='/etc/nmsprime/env'
source "$env/root.env"

# unfortunately dhcpd does not support hmacs other than hmac-md5
# see: https://bugs.centos.org/view.php?id=12107
dnsSecret=$(ddns-confgen -a hmac-md5 -r /dev/urandom | grep secret)
radius_psw=$(pwgen 12 1)

# create folders
install -dm750 /etc/dhcp-nmsprime/cmts_gws

# change owner
chown -R apache:dhcpd /etc/dhcp-nmsprime
chown -R apache /tftpboot
chown -R named:named /var/named/dynamic
chown apache:dhcpd /etc/named-ddns.sh
chmod 750 /etc/named-ddns.sh

sed -i "s|^.*secret \"<DNS-PASSWORD>\";|$dnsSecret|" /etc/dhcp-nmsprime/dhcpd.conf
sed -i "s|^.*secret \"<DNS-PASSWORD>\";|$dnsSecret|" /etc/named-nmsprime.conf
dnsPw=$(echo $dnsSecret | cut -d '"' -f2)
sed -i "s/<DNS-PASSWORD>/$dnsPw/" /etc/named-ddns.sh
sudo -u postgres /usr/pgsql-13/bin/psql nmsprime -c "UPDATE nmsprime.provbase set dns_password = '$dnsPw'"

openssl rand -hex 32 > /etc/named-ddns-cpe.key
chown apache:dhcpd /etc/named-ddns-cpe.key
chmod 640 /etc/named-ddns-cpe.key

sed -i "s/<hostname>/$(hostname | cut -d '.' -f1)/" /var/named/dynamic/{nmsprime.test,in-addr.arpa}.zone

echo $'\ninclude /etc/chrony.d/*.conf' >> /etc/chrony.conf

systemctl daemon-reload

systemctl enable chronyd
systemctl enable dhcpd
systemctl enable named
systemctl enable nmsprimed
systemctl enable xinetd

# starting dhcpd won't work now, because not all files have been populated
systemctl start chronyd
systemctl start named
systemctl start nmsprimed
systemctl start xinetd

firewall-cmd --reload

# create freeradius DB and user
user='radius'
sudo -u postgres /usr/pgsql-13/bin/psql -c 'CREATE DATABASE radius'
sudo -u postgres /usr/pgsql-13/bin/psql -d radius -c "
    CREATE USER $user PASSWORD '$radius_psw';
    GRANT USAGE ON SCHEMA public TO $user;
    GRANT ALL PRIVILEGES ON ALL Tables in schema public TO $user;
    GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO $user;"
sed -i "s/RADIUS_DB_PASSWORD=$/RADIUS_DB_PASSWORD=$radius_psw/" "$env/provbase.env"

systemd-tmpfiles --create
