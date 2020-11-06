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
            'name'          => 'The :Sender changed name of board from `:old` to `:new`'
        ],
    ],

    'budget'=> [
        'changed' => [
            'soft_budget'   => 'from `:old` to `:new` in the task `:Task`',
            'hard_budget'   => 'from `:old` to `:new` in the task `:Task`',
        ],
        'over' => [
            'soft_budget'   => 'Over soft budget',
            'hard_budget'   => 'Over hard budget',
            'both'          => 'Over soft and hard budget'
        ]
    ],

    'group'=> [
        'changed' => [
            'name'          => ':Sender changed name of group from `:old` to `:new`'
        ],
    ],

    'task' => [
        'deadline' => 'Over deadline time for task: :task',
        'action' => [
            'attach'        => ':Sender has :action :Receiver to the task `:Task`',
            'subscribe'     => 'You was :action notification at `:Task` task, by :Sender',
        ],
        'changed' => [
            'name'          => 'from `:old` to `:new`',
            'priority'          => 'from `:old` to `:new`',
            'done_by'       => ':action the task',
        ],
    ],

    'web_push' => [

        'budget' => [
            'title' => 'Budget notification'
        ],

        'deadline' => [
            'title' => 'Deadline notification',
            'message' => 'Over deadline'
        ]
    ]
];