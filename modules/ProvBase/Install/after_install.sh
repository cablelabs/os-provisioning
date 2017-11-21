dir="/var/www/nmsprime"
pw=$(openssl rand -base64 16)

# create folders
install -dm750 /etc/dhcp/nmsprime/cmts_gws
install -dm750 -g named /var/named-nmsprime

# change owner
chown -R apache:dhcpd /etc/dhcp/nmsprime
chown -R apache /tftpboot

sed -i "s|<DNS-PASSWORD>|$pw|" /etc/dhcp/nmsprime/dhcpd.conf
sed -i "s|<DNS-PASSWORD>|$pw|" /etc/named-nmsprime.conf

systemctl daemon-reload

systemctl enable chronyd
systemctl enable dhcpd
systemctl enable named
systemctl enable nmsprimed
systemctl enable xinetd

systemctl start chronyd
systemctl start nmsprimed
systemctl start xinetd
