<?php

return [
    'options' => [
        'period' => [
            ['id' => 1, 'name' => 'Today'],
            ['id' => 2, 'name' => 'Yesterday'],
            ['id' => 3, 'name' => 'This Week'],
            ['id' => 4, 'name' => 'Last Week'],
            ['id' => 5, 'name' => 'Last 14 Days'],
            ['id' => 6, 'name' => 'This Month'],
            ['id' => 7, 'name' => 'Last Month'],
            ['id' => 8, 'name' => 'Custom']
        ],
        'bill' => [
            ['id' => 1, 'name' => 'All'],
            ['id' => 2, 'name' => 'Billed Time'],
            ['id' => 3, 'name' => 'Not Billed Time (With Parked Time)'],
            ['id' => 4, 'name' => 'Not Billed Time (Without Parked Time)'],
            ['id' => 5, 'name' => 'Parked Time'],
            ['id' => 6, 'name' => 'Not Billable'],
        ],
        'time_filter' => [
            ['id' => 1, 'name' => 'All'],
            ['id' => 2, 'name' => 'No Time Under 2 Min'],
            ['id' => 3, 'name' => 'No Time Under 5 Min'],
            ['id' => 4, 'name' => 'No Time Under 15 Min'],
            ['id' => 5, 'name' => 'No Time Under 30 Min'],
            ['id' => 6, 'name' => 'No Time Under 60 Min']
        ],
    ],
    'default' => [
        'selectPeriod' => [8],
        'selectBill' => [1],
        'selectTimeFilter' => [1],
        'selectMembers' => [1]
    ],
    'casts' => [
        'selectPeriod' => 'period',
        'selectBill' => 'bill',
        'selectTimeFilter' => 'time_filter',
        'selectMembers' => 'members'
    ],
];
