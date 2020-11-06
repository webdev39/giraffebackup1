<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Activity Messages
    |--------------------------------------------------------------------------
    */

    'board'=> [
        'changed' => [
            'name'          => ':Sender changed name of board from `:old_value` to `:new_value`'
        ],
    ],

    'group'=> [
        'detach_member_not_allowed' => 'You can\'t delete last user in group',
        'changed' => [
            'name'          => ':Sender changed name of group from `:old_value` to `:new_value`'
        ],
    ],

    'task' => [
        'action' => [
            'attach'        => ':Sender has :action :Receiver to the task',
            'assigned_and_subscribed' => ':Sender has :action :Receiver to the task',
            'deleted' => ":Sender has :action the task"
        ],
        'changed' => [
            'soft_budget'   => ':Sender changed soft budget from `:old_value` to `:new_value`',
            'hard_budget'   => ':Sender changed hard budget from `:old_value` to `:new_value`',
            'name'          => ':Sender changed name of task from `:old_value` to `:new_value`',
            'description'   => [
                'sender'    => ':sender',
                'action'    => 'changed the description of',
                'task'      => ':task',
                'new'       => ':new_value',
                'old'       => ':old_value'
            ],
            'priority_id'   => ':Sender changed priority of task from `:old_name` to `:new_name`',
            'sort_weight'   => ':Sender :action sorting of task',
            'done_by'       => ':Sender :action the task `:Task`',
            'is_archive'    => ':Sender :action the task `:Task`',
            'deadline'      => ':Sender :action the deadline from :old_value to :new_value',
            'planned_deadline' => ':Sender changed todo of task to `:new_value`',
        ],
        'set' => [
            'deadline'      => ':Sender set the deadline to :new_value',
            'description'   => ':Sender set the description of ":task" to ":new_value"',
        ],
    ],
];
