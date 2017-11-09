echo "mibdirs +/var/www/nmaprime/storage/app/data/hfcsnmp/mibs" >> /etc/snmp/snmp.conf
chmod 644 /etc/snmp/snmp.conf         # apache must be able to read this file when executing snmptranslate
