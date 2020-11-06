<?php

return [
    'settings' => [
        'logo'          => 'images/aconi.jpg',
        'name'          => 'ACONI OC',
        'creator'       => 'ACONI OC',
        'author'        => 'ACONI GmbH',
        'title'         => 'ACONI Rechnung #{bill.invoice_number}',
        'subject'       => 'ACONI Rechnung #{bill.invoice_number}',
        'keywords'      => 'bill, rechnung, aconi, internet, seo, sea',
        'email'         => 'info@aconi.com',
        'phone'         => '05921-7111490',
        'web'           => 'www.aconi.com',
        'postcode'      => '48529',
        'street'        => 'Morsstiege 31',
        'city'          => 'Nordhorn',
        'date_format'   => 'YYYY-MM-DD HH.mm',
        'money_format'  => ['.', ','],
        'currency_id'   => null,
        'fee'           => 19,
        'font_id'       => null,
        'font_size'     => 20,
        'filename'      => 'aconi_r{bill.invoice_number}_k{customer.id}',
        'bill_settings' => [
            'logo_position' => 'right',
            'logo_height'   => '100',
        ],
    ],

    'templates' => [
        'bill' => [
            'content'   => 'bill.content',
        ]
    ],

//    'table' => [
//        'header' => [
//            'task' => 'Projekt/Task',
//            'comment' => 'Kommentar',
//            'time' => 'Dauer/Anzahl',
//            'rate' => 'Satz',
//            'summ' => 'Summe',
//        ],
//        'label' => [
//            'project' => 'Projekt',
//            'task' => 'Aufgabe',
//            'sum' => 'Gesamt Netto',
//            'vat' => 'zzgl. 19% MwSt.',
//            'total' => 'Gesamt Brutto',
//        ],
//    ]
];
