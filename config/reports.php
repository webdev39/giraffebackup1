<?php

return [
    'options' => [
        'timerange' => [
            ['id' => 1, 'name' => 'Today'],
            ['id' => 2, 'name' => 'Yesterday'],
            ['id' => 3, 'name' => 'This Week'],
            ['id' => 4, 'name' => 'Last Week'],
            ['id' => 5, 'name' => 'Last 14 Days'],
            ['id' => 6, 'name' => 'This Month'],
            ['id' => 7, 'name' => 'Last Month'],
            ['id' => 8, 'name' => 'Custom']
        ],
        'show' => [
            ['id' => 1, 'name' => 'Show Group'],
            ['id' => 2, 'name' => 'Show Board'],
            ['id' => 3, 'name' => 'Show Task'],
            ['id' => 4, 'name' => 'Show User'],
            ['id' => 5, 'name' => 'Show Comment'],
            ['id' => 11, 'name' => 'Last changes'],
            ['id' => 9, 'name' => 'Show Bill-Status'],
            ['id' => 7, 'name' => 'Show Used-Time'],
            ['id' => 8, 'name' => 'Show Bill-Time'],
            ['id' => 6, 'name' => 'Show Billed-Time'],
            ['id' => 10,'name' => 'Show Unbilled-Time'],
        ],
        'grouping' => [
            ['id' => 1, 'name' => 'None'],
            ['id' => 2, 'name' => 'By Day'],
            ['id' => 3, 'name' => 'By Week'],
            ['id' => 4, 'name' => 'By Month'],
            ['id' => 5, 'name' => 'By Year']
        ],
        'details' => [
            ['id' => 1, 'name' => 'Details'],
            ['id' => 2, 'name' => 'Summary']
        ]
    ],
    'default' => [
        'selectTimeranges'  => [6],
        'selectShowOptions' => [1, 2, 3, 4, 5, 11, 9, 7, 8],
        'selectGrouping'    => [1],
        'selectDetails'     => [1]
    ],
    'casts' =>[
        'selectTimeranges' => 'timerange',
        'selectShowOptions' => 'show',
        'selectGrouping' => 'grouping',
        'selectDetails' => 'details'
    ]

];