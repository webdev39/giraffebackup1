<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notification Messages
    |--------------------------------------------------------------------------
    */

    'comment'   => [
        'like'              => '`:comment`',
        'create'            => '`:comment`',
    ],

    'board'=> [
        'changed' => [
            'name'          => ':Sender hat den Name des Boards von `:old` zu `:new` geändert'
        ],
    ],

    'budget'=> [
        'changed' => [
            'soft_budget'   => 'von `:old` zu `:new` in der Aufgabe `:Task`',
            'hard_budget'   => 'von `:old` zu `:new` in der Aufgabe `:Task`',
        ],
        'over' => [
            'soft_budget'   => 'Budgetwarnung wurde erreicht',
            'hard_budget'   => 'Budgetlimit wurde erreicht',
            'both'          => 'Budgetwarnung und Maximalbudget wurden erreicht'
        ]
    ],

    'group'=> [
        'changed' => [
            'name'          => ':Sender hat den Namen der Gruppe von `:old` zu `:new` geändert'
        ],
    ],

    'task' => [
        'deadline' => 'Die Deadline der Aufgabe :task wurde erreicht',
        'action' => [
            'attach'        => ':Sender hat :Receiver zur Aufgabe `:Task` :action',
            'subscribe'     => 'Du wurdest von :Sender zur Aufgabe `:Task` :action',
        ],
        'changed' => [
            'name'          => 'von `:old` zu `:new`',
            'priority'      => 'von `:old` zu `:new`',
            'done_by'       => ':action der Aufgabe',
        ],
    ],

    'web_push' => [

        'budget' => [
            'title' => 'Budgetbenachrichtigung'
        ],

        'deadline' => [
            'title' => 'Fristbenachrichtigungen',
            'message' => 'Über die Frist hinaus'
        ]
    ]
];