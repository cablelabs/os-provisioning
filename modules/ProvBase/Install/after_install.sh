dir="/var/www/nmsprime"
pw=$(openssl rand -base64 16)

# create folders
install -dm750 /etc/dhcp/nmsprime/cmts_gws

# change owner
chown -R apache:dhcpd /etc/dhcp/nmsprime
chown -R apache /tftpboot
chown -R named:named /var/named/dynamic

sed -i "s|<DNS-PASSWORD>|$pw|" /etc/dhcp/nmsprime/dhcpd.conf
sed -i "s|<DNS-PASSWORD>|$pw|" /etc/named-nmsprime.conf
sed -i "s/<hostname>/$(hostname | cut -d '.' -f1)/" /var/named/dynamic/{nmsprime.test,in-addr.arpa}.zone

systemctl daemon-reload

systemctl enable chronyd
systemctl enable dhcpd
systemctl enable named
systemctl enable nmsprimed
systemctl enable xinetd

systemctl start chronyd
systemctl start nmsprimed
systemctl start xinetd
