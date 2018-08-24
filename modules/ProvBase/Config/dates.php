<?php
/**
 * Returns dates array for calculating sales
 */
return [
    'last_month' => [
        'today' 		=> date('Y-m-d'),
        'm' 			=> date('m'),
        'Y' 			=> date('Y', strtotime('first day of last month')),
        'this_m'	 	=> date('Y-m'),
        'thism_01'		=> date('Y-m-01'),
        'thism_bill'	=> date('m/Y'),
        'lastm'			=> date('m', strtotime('first day of last month')),			// written this way because of known bug ("-1 month" or "last month" is erroneous)
        'lastm_01' 		=> date('Y-m-01', strtotime('first day of last month')),
        'lastm_bill'	=> date('m/Y', strtotime('first day of last month')),
        'lastm_Y'		=> date('Y-m', strtotime('first day of last month')),		// strtotime(first day of last month) is integer with actual timestamp!
        'nextm_01' 		=> date('Y-m-01', strtotime('+1 month')),
        'null' 			=> '0000-00-00',
        'm_in_sec' 		=> 60 * 60 * 24 * 30,												// month in seconds
        'last_run'		=> '0000-00-00', 											// filled on start of execution
    ],
    'current_month' => [
        'today' 		=> date('Y-m-d'),
        'm' 			=> date('m'),
        'Y' 			=> date('Y'),
        'this_m'	 	=> date('Y-m'),
        'thism_01'		=> date('Y-m-01'),
        'thism_bill'	=> date('m/Y'),
        'lastm'			=> date('m'),			// written this way because of known bug ("-1 month" or "last month" is erroneous)
        'lastm_01' 		=> date('Y-m-01'),
        'lastm_bill'	=> date('m/Y'),
        'lastm_Y'		=> date('Y-m'),		// strtotime(first day of next month) is integer with actual timestamp!
        'nextm_01' 		=> date('Y-m-01', strtotime('+1 month')),
        'null' 			=> '0000-00-00',
        'm_in_sec' 		=> 60 * 60 * 24 * 30,												// month in seconds
        'last_run'		=> '0000-00-00', 											// filled on start of execution
    ],
    'next_month' => [
        'today' 		=> date('Y-m-d'),
        'm' 			=> date('m'),
        'Y' 			=> date('Y', strtotime('first day of next month')),
        'this_m'	 	=> date('Y-m'),
        'thism_01'		=> date('Y-m-01'),
        'thism_bill'	=> date('m/Y'),
        'lastm'			=> date('m', strtotime('first day of next month')),			// written this way because of known bug ("-1 month" or "last month" is erroneous)
        'lastm_01' 		=> date('Y-m-01', strtotime('first day of next month')),
        'lastm_bill'	=> date('m/Y', strtotime('first day of next month')),
        'lastm_Y'		=> date('Y-m', strtotime('first day of next month')),		// strtotime(first day of next month) is integer with actual timestamp!
        'nextm_01' 		=> date('Y-m-01', strtotime('+1 month')),
        'null' 			=> '0000-00-00',
        'm_in_sec' 		=> 60 * 60 * 24 * 30,												// month in seconds
        'last_run'		=> '0000-00-00', 											// filled on start of execution
    ],
];
