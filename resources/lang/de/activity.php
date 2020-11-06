<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Activity Messages
    |--------------------------------------------------------------------------
    */

    'board'=> [
        'changed' => [
            'name'          => ':Sender hat den Namen des Boards geändert von `:old_value` zu `:new_value`'
        ],
    ],

    'group'=> [
        'detach_member_not_allowed' => 'Der letzte Benutzer der Gruppe kann nicht gelöscht werden',
        'changed' => [
            'name'          => ':Sender hat den Namen der Gruppe von `:old_value` zu `:new_value geändert`'
        ],
    ],

    'task' => [
        'action' => [
            'attach'        => ':Sender hat :Receiver to the task :action ',
            'assigned_and_subscribed' => ':Sender hat :Receiver zur Aufgabe :action ',
            'deleted' => ":Sender hat die Aufgabe :action"
        ],
        'changed' => [
            'soft_budget'   => ':Sender hat die Bugetwarnung von `:old_value` zu `:new_value` geändert',
            'hard_budget'   => ':Sender hat das Budgetlimit von `:old_value` zu `:new_value` geändert',
            'name'          => ':Sender hat den Titel der Aufgabe von `:old_value` zu `:new_value` geändert',
            'description'   => ':Sender hat die Aufgabenbeschreibung der Aufgabe ":task" von ":old_value" zu ":new_value" geändert',
            'priority_id'   => ':Sender hat die Priorität von `:old_name` zu `:new_name` geändert',
            'sort_weight'   => ':Sender hat die Sortingung der Aufgabe :action',
            'done_by'       => ':Sender hat  die Aufgabe `:Task` :action',
            'is_archive'    => ':Sender hat die Aufgabe `:Task` :action',
            'deadline'      => ':Sender hat die Deadline von :old_value zu :new_value :action',
            'planned_deadline' => ':Sender hat das ToDo der Aufgabe zu `:new_value` geändert',
        ],
        'set' => [
            'deadline'      => ':Sender hat die Deadline :new_value eingestellt',
            'description'   => ':Sender hat die Beschreibung von ":task" eingestellt zu ":new_value"',
        ],
    ],
];
