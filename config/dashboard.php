<?php

$i = 1;
while ($i < 13) {
    if ($i < 10) {
        $month = '0'.$i.'/'.date('Y');
    } else {
        $month = $i.'/'.date('Y');
    }

    $labels[] = $month;
    $contracts[] = 0;
    $i++;
}

return [
    'name' => 'Dashboard',
    'contracts' => [
        'labels' => $labels,
        'contracts' => $contracts,
    ],
    'income' => [
        'labels' => ['Internet', 'Voip', 'TV', 'Other'],
        'data' => [0.0, 0.0, 0.0, 0.0],
    ],
];
