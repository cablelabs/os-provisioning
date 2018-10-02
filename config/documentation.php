<?php

/*
 * The official documentation array which will be linked for official HELP
 */

// prefer retrieved documentation over installed one
$file = storage_path('app/data/dashboard/documentation.json');
if (file_exists($file) && $ret = json_decode(file_get_contents($file), true)) {
    if (is_array($ret) && ! empty($ret)) {
        return $ret;
    }
}

return [

// Prov Base

    'cmts' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/CMTS+setup',
                'youtube' => 'https://www.youtube.com/watch?v=g-OChuE3mf4',
                'url' => null, ],

    'configfile' => ['doc' => null, // todo
                'youtube' => 'https://www.youtube.com/watch?v=aYjuWXhaV3s&t=500s', // jump to 500s
                'url' => null, ],

    'contract' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/Provisioning',
                'youtube' => 'https://www.youtube.com/watch?v=t-PFsy42cI0',
                'url' => null, ],

    'domain' => null, // todo

    'endpoint' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/IP+address+assignment',
                    'youtube' => 'https://www.youtube.com/watch?v=D3m8RyKnO38',
                    'url' => null, ],

    'ippool' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/Network',
                    'youtube' => 'https://www.youtube.com/watch?v=aYjuWXhaV3s&t=240s', // jump to 240s
                    'url' => null, ],

    'modem' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/Cable+Modem+Registration',
                    'youtube' => 'https://www.youtube.com/watch?v=t-PFsy42cI0&t=40s', // jump to 40s
                    'url' => null, ],

    'qos' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/QoS+to+Configfile+Mapping',
                'youtube' => 'https://www.youtube.com/watch?v=aYjuWXhaV3s&t=378s', // jump to 378s
                'url' => null, ],

// ProvMon

    'modem_analysis' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/Monitoring',
                         'youtube' => 'https://www.youtube.com/watch?v=KnCBDpKxi9g&t=144s', // jump to 145s
                         'url' => null, ],

    'cpe_analysis' => ['doc' => null,
                     'youtube' => 'https://www.youtube.com/watch?v=9xc4Jlhg7fY&t=675s', // jump to 675s
                     'url' => null, ],

    'mta_analysis' => ['doc' => null,
                     'youtube' => 'https://www.youtube.com/watch?v=9xc4Jlhg7fY&t=785s', // jump to 785s
                     'url' => null, ],

    'cmts_analysis' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/CMTS+Analysis+and+Monitoring',
                        'youtube' => 'https://www.youtube.com/watch?v=a3UOtK9cduY&t=290s', // jump to 290s
                        'url' => null, ],
// ProvVoip

    'phonenumber' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/VoIP',
                        'youtube' => null, // TODO
                        'url' => null, ],

    'mta' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/VoIP+Provisioning',
                'youtube' => null, // TODO
                'url' => null, ],

// HFC

    'netelement' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/IT+Maintenance',
                    'youtube' => 'https://www.youtube.com/watch?v=dq4x_KD2q7M',
                    'url' => null, ],

// Billing

    'product' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/Billing',
                  'youtube' => 'https://www.youtube.com/watch?v=jZrE2YAvCi8',
                  'url' => null, ],

    'costcenter' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/Accounting', // TODO
                  'youtube' => 'https://www.youtube.com/watch?v=QDsxx6oe4mw&t=525s', // TODO
                  'url' => null, ],

    'settlementrun' => ['doc' => 'https://devel.roetzer-engineering.com/confluence/display/NMS/Accounting',
                  'youtube' => 'https://www.youtube.com/watch?v=QDsxx6oe4mw&t=235s',
                  'url' => null, ],

    'company' => ['doc' => null,
                  'youtube' => 'https://www.youtube.com/watch?v=QDsxx6oe4mw&t=400s',
                  'url' => null, ],

    'sepaaccount' => ['doc' => null,
                      'youtube' => 'https://www.youtube.com/watch?v=QDsxx6oe4mw&t=455s',
                      'url' => null, ],

    'salesman' => ['doc' => null,
                'youtube' => 'https://www.youtube.com/watch?v=wCsIElIBPfc',
                'url' => null, ],

    'numberrange' => ['doc' => null,
                'youtube' => 'https://www.youtube.com/watch?v=QDsxx6oe4mw&t=600s',
                'url' => null, ],

];
