<?php

class InstallNamedDdns extends BaseMigration
{
    protected $tablename = '';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        chown('/etc/named-ddns.sh', 'dhcpd');
        chmod('/etc/named-ddns.sh', 0750);

        // create secret to salt hostname generation of public CPEs
        file_put_contents('/etc/named-ddns-cpe.key', bin2hex(random_bytes(32)).PHP_EOL);
        chown('/etc/named-ddns-cpe.key', 'apache');
        chgrp('/etc/named-ddns-cpe.key', 'dhcpd');
        chmod('/etc/named-ddns-cpe.key', 0640);

        // get DNS-PASSWORD
        if (preg_match('/secret +"([^"]+)"/', file_get_contents('/etc/dhcp-nmsprime/dhcpd.conf'), $m)) {
            $pw = $m[1];
        } else {
            return;
        }

        $str = file_get_contents('/etc/nmsprime/env/global.env');
        $str = preg_replace('/^DNS_PASSWORD=<DNS-PASSWORD>$/m', "DNS_PASSWORD=$pw", $str);
        file_put_contents('/etc/nmsprime/env/global.env', $str);

        $str = file_get_contents('/etc/named-ddns.sh');
        $str = str_replace('<DNS-PASSWORD>', "$pw", $str);
        file_put_contents('/etc/named-ddns.sh', $str);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        unlink('/etc/named-ddns-cpe.key');
    }
}
