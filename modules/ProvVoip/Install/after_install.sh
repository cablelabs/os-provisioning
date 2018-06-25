# change owner
chown -R apache:dhcpd /etc/dhcp-nmsprime
sed -i '/^#.*mta.conf";/s/^#//' /etc/dhcp-nmsprime/dhcpd.conf

firewall-cmd --reload
