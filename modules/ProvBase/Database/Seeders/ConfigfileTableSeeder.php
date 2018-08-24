<?php

namespace Modules\ProvBase\Database\Seeders;

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Modules\ProvBase\Entities\Configfile;

class ConfigfileTableSeeder extends \BaseSeeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, self::$max_seed_l2) as $index) {
            Configfile::create([
                'name' => $faker->colorName(),
                'parent_id' => 0,
                'device' => (rand(0, 100) > 30 ? 1 : 2),
                'text' => 'SnmpMibObject sysLocation.0 String "Test Lab" ;',
            ]);
        }

        // add two firmware dummies
        $firmware_dummies = ['fw_dummy1_v3.7.12.bin', 'fw_dummy2_v1.7-fix12.bin'];
        foreach ($firmware_dummies as $firmware_dummy) {
            touch('/tftpboot/fw/'.$firmware_dummy);
        }

        // add running configfiles for cm and mta
        // this is really ugly – so I put it at the end of the file ;-)
        Configfile::create([
            'name' => 'cm-base',
            'parent_id' => 0,
            'device' => 'cm',
            'type' => 'generic',
            'public' => 'yes',
            'text' => <<<'EOT'
ModemCapabilities
{
    ConcatenationSupport 1;
    IGMPSupport 1;
}
SnmpMibObject sysLocation.0 String {test};
SnmpMibObject docsDevNmAccessIp.10 IPAddress 172.20.0.0 ;
SnmpMibObject docsDevNmAccessIp.20 IPAddress 1.2.3.4 ;
SnmpMibObject docsDevNmAccessIp.30 IPAddress 1.2.3.4 ;
SnmpMibObject docsDevNmAccessIp.40 IPAddress 172.22.0.0 ;
SnmpMibObject docsDevNmAccessIp.50 IPAddress 1.2.3.4 ;
SnmpMibObject docsDevNmAccessIp.60 IPAddress 1.2.3.4 ;
SnmpMibObject docsDevNmAccessIpMask.10 IPAddress 255.255.0.0 ;
SnmpMibObject docsDevNmAccessIpMask.20 IPAddress 255.255.255.0 ;
SnmpMibObject docsDevNmAccessIpMask.30 IPAddress 255.255.255.0 ;
SnmpMibObject docsDevNmAccessIpMask.40 IPAddress 255.255.0.0 ;
SnmpMibObject docsDevNmAccessIpMask.50 IPAddress 255.255.255.0 ;
SnmpMibObject docsDevNmAccessIpMask.60 IPAddress 255.255.255.0 ;
SnmpMibObject docsDevNmAccessCommunity.10 String "public" ;
SnmpMibObject docsDevNmAccessCommunity.20 String "public" ;
SnmpMibObject docsDevNmAccessCommunity.30 String "public" ;
SnmpMibObject docsDevNmAccessCommunity.40 String "private" ;
SnmpMibObject docsDevNmAccessCommunity.50 String "private" ;
SnmpMibObject docsDevNmAccessCommunity.60 String "private" ;
SnmpMibObject docsDevNmAccessControl.10 Integer {test}; /* read */
SnmpMibObject docsDevNmAccessControl.20 Integer 2; /* read */
SnmpMibObject docsDevNmAccessControl.30 Integer 2; /* read */
SnmpMibObject docsDevNmAccessControl.40 Integer 3; /* readWrite */
SnmpMibObject docsDevNmAccessControl.50 Integer 3; /* readWrite */
SnmpMibObject docsDevNmAccessControl.60 Integer 3; /* readWrite */
SnmpMibObject docsDevNmAccessInterfaces.10 String "@" ;
SnmpMibObject docsDevNmAccessInterfaces.20 String "@" ;
SnmpMibObject docsDevNmAccessInterfaces.30 String "@" ;
SnmpMibObject docsDevNmAccessInterfaces.40 String "@" ;
SnmpMibObject docsDevNmAccessInterfaces.50 String "@" ;
SnmpMibObject docsDevNmAccessInterfaces.60 String "@" ;
SnmpMibObject docsDevNmAccessStatus.10 Integer 4; /* createAndGo */
SnmpMibObject docsDevNmAccessStatus.20 Integer 4; /* createAndGo */
SnmpMibObject docsDevNmAccessStatus.30 Integer 4; /* createAndGo */
SnmpMibObject docsDevNmAccessStatus.40 Integer 4; /* createAndGo */
SnmpMibObject docsDevNmAccessStatus.50 Integer 4; /* createAndGo */
SnmpMibObject docsDevNmAccessStatus.60 Integer 4; /* createAndGo */
SnmpMibObject docsDevFilterLLCUnmatchedAction.0 Integer 1; /* discard */
SnmpMibObject docsDevFilterLLCStatus.1 Integer 4; /* createAndGo */
SnmpMibObject docsDevFilterLLCStatus.2 Integer 4; /* createAndGo */
SnmpMibObject docsDevFilterLLCIfIndex.1 Integer 0 ;
SnmpMibObject docsDevFilterLLCIfIndex.2 Integer 0 ;
SnmpMibObject docsDevFilterLLCProtocolType.1 Integer 1; /* ethertype */
SnmpMibObject docsDevFilterLLCProtocolType.2 Integer 1; /* ethertype */
SnmpMibObject docsDevFilterLLCProtocol.1 Integer 2048 ;
SnmpMibObject docsDevFilterLLCProtocol.2 Integer 2054 ;
SnmpMibObject docsDevFilterIpDefault.0 Integer 2; /* accept */
SnmpMibObject docsDevFilterIpStatus.3 Integer 4; /* createAndGo */
SnmpMibObject docsDevFilterIpControl.3 Integer 1; /* discard */
SnmpMibObject docsDevFilterIpIfIndex.3 Integer 1 ;
SnmpMibObject docsDevFilterIpDirection.3 Integer 1; /* inbound */
SnmpMibObject docsDevFilterIpBroadcast.3 Integer 2; /* false */
SnmpMibObject docsDevFilterIpSaddr.3 IPAddress 0.0.0.0 ;
SnmpMibObject docsDevFilterIpSmask.3 IPAddress 0.0.0.0 ;
SnmpMibObject docsDevFilterIpDaddr.3 IPAddress 0.0.0.0 ;
SnmpMibObject docsDevFilterIpDmask.3 IPAddress 0.0.0.0 ;
SnmpMibObject docsDevFilterIpProtocol.3 Integer 17 ;
SnmpMibObject docsDevFilterIpSourcePortLow.3 Integer 67 ;
SnmpMibObject docsDevFilterIpSourcePortHigh.3 Integer 67 ;
SnmpMibObject docsDevFilterIpDestPortLow.3 Integer 0 ;
SnmpMibObject docsDevFilterIpDestPortHigh.3 Integer 65535 ;
BaselinePrivacy
{
    AuthTimeout 10;
    ReAuthTimeout 10;
    AuthGraceTime 600;
    OperTimeout 10;
    ReKeyTimeout 10;
    TEKGraceTime 1800;
    AuthRejectTimeout 60;
}
UsServiceFlow
{
    UsServiceFlowRef 101;
    QosParamSetType 7;
    MaxRateSustained {qos.us_rate_max_help.0};
}
DsServiceFlow
{
    DsServiceFlowRef 1;
    QosParamSetType 7;
    MaxRateSustained {qos.ds_rate_max_help.0};
}
GlobalPrivacyEnable 1;
EOT
        ]);

        Configfile::create([
            'name' => 'Fritz!Box 6360',
            'parent_id' => 0,
            'device' => 'mta',
            'type' => 'generic',
            'public' => 'yes',
            'text' => <<<'EOT'
MtaConfigDelimiter 1;
SnmpMibObject mib-2.140.1.1.6.0 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.1 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.2 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.3 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.4 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.5 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.6 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.7 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.8 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.9 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.10.10 Integer 1 ;
SnmpMibObject enterprises.872.1.4.2.1.12.1 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.12.2 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.12.3 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.12.4 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.12.5 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.12.6 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.12.7 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.12.8 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.12.9 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.12.10 String "sip.server.net" ;
SnmpMibObject enterprises.872.1.4.2.1.21.1 Integer 2 ;
SnmpMibObject enterprises.872.1.4.2.1.21.2 Integer 2 ;
SnmpMibObject enterprises.872.1.4.2.1.21.3 Integer 2 ;
SnmpMibObject enterprises.872.1.4.2.1.21.4 Integer 2 ;
SnmpMibObject enterprises.872.1.4.2.1.21.5 Integer 2 ;
SnmpMibObject enterprises.872.1.4.2.1.21.6 Integer 2 ;
SnmpMibObject enterprises.872.1.4.2.1.21.7 Integer 2 ;
SnmpMibObject enterprises.872.1.4.2.1.21.8 Integer 2 ;
SnmpMibObject enterprises.872.1.4.2.1.21.9 Integer 2 ;
SnmpMibObject enterprises.872.1.4.2.1.21.10 Integer 2 ;
SnmpMibObject enterprises.872.1.4.3.1.3.1 String "{phonenumber.prefix_number.0}{phonenumber.number.0}" ;
SnmpMibObject enterprises.872.1.4.3.1.3.2 String "{phonenumber.prefix_number.1}{phonenumber.number.1}" ;
SnmpMibObject enterprises.872.1.4.3.1.3.3 String "{phonenumber.prefix_number.2}{phonenumber.number.2}" ;
SnmpMibObject enterprises.872.1.4.3.1.3.4 String "{phonenumber.prefix_number.3}{phonenumber.number.3}" ;
SnmpMibObject enterprises.872.1.4.3.1.3.5 String "{phonenumber.prefix_number.4}{phonenumber.number.4}" ;
SnmpMibObject enterprises.872.1.4.3.1.3.6 String "{phonenumber.prefix_number.5}{phonenumber.number.5}" ;
SnmpMibObject enterprises.872.1.4.3.1.3.7 String "{phonenumber.prefix_number.6}{phonenumber.number.6}" ;
SnmpMibObject enterprises.872.1.4.3.1.3.8 String "{phonenumber.prefix_number.7}{phonenumber.number.7}" ;
SnmpMibObject enterprises.872.1.4.3.1.3.9 String "{phonenumber.prefix_number.8}{phonenumber.number.8}" ;
SnmpMibObject enterprises.872.1.4.3.1.3.10 String "{phonenumber.prefix_number.9}{phonenumber.number.9}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.1 String "{phonenumber.username.0}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.2 String "{phonenumber.username.1}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.3 String "{phonenumber.username.2}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.4 String "{phonenumber.username.3}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.5 String "{phonenumber.username.4}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.6 String "{phonenumber.username.5}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.7 String "{phonenumber.username.6}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.8 String "{phonenumber.username.7}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.9 String "{phonenumber.username.8}" ;
SnmpMibObject enterprises.872.1.4.3.1.4.10 String "{phonenumber.username.9}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.1 String "{phonenumber.password.0}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.2 String "{phonenumber.password.1}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.3 String "{phonenumber.password.2}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.4 String "{phonenumber.password.3}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.5 String "{phonenumber.password.4}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.6 String "{phonenumber.password.5}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.7 String "{phonenumber.password.6}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.8 String "{phonenumber.password.7}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.9 String "{phonenumber.password.8}" ;
SnmpMibObject enterprises.872.1.4.3.1.5.10 String "{phonenumber.password.9}" ;
SnmpMibObject snmpTargetAddrTDomain.'admin' ObjectID .1.3.6.1.6.1.1 ;
SnmpMibObject snmpTargetAddrTDomain.'operator' ObjectID .1.3.6.1.6.1.1 ;
SnmpMibObject snmpTargetAddrTAddress.'admin' HexString 0xac16fd010000 ;
SnmpMibObject snmpTargetAddrTAddress.'operator' HexString 0xac16fd010000 ;
SnmpMibObject snmpTargetAddrTagList.'admin' String "adminTag" ;
SnmpMibObject snmpTargetAddrTagList.'operator' String "operatorTag" ;
SnmpMibObject snmpTargetAddrParams.'admin' String "admin" ;
SnmpMibObject snmpTargetAddrParams.'operator' String "operator" ;
SnmpMibObject snmpTargetAddrRowStatus.'admin' Integer 4; /* createAndGo */
SnmpMibObject snmpTargetAddrRowStatus.'operator' Integer 4; /* createAndGo */
SnmpMibObject snmpCommunityName.'admin' String "private" ;
SnmpMibObject snmpCommunityName.'operator' String "public" ;
SnmpMibObject snmpCommunitySecurityName.'admin' String "admin" ;
SnmpMibObject snmpCommunitySecurityName.'operator' String "operator" ;
SnmpMibObject snmpCommunityTransportTag.'admin' String "adminTag" ;
SnmpMibObject snmpCommunityTransportTag.'operator' String "operatorTag" ;
SnmpMibObject snmpCommunityStorageType.'admin' Integer 2; /* volatile */
SnmpMibObject snmpCommunityStorageType.'operator' Integer 2; /* volatile */
SnmpMibObject snmpCommunityStatus.'admin' Integer 4; /* createAndGo */
SnmpMibObject snmpCommunityStatus.'operator' Integer 4; /* createAndGo */
SnmpMibObject snmpTargetAddrTMask.'admin' HexString 0xffffffff0000 ;
SnmpMibObject snmpTargetAddrTMask.'operator' HexString 0xffffffff0000 ;
SnmpMibObject snmpTargetAddrMMS.'admin' Integer 0 ;
SnmpMibObject snmpTargetAddrMMS.'operator' Integer 0 ;
SnmpMibObject pktcMtaDevProvConfigHash.0 HexString 0x796cef93130a8f71447783944d93092de2eb1ba1 ;
MtaConfigDelimiter 255;
EOT
        ]);
    }
}
