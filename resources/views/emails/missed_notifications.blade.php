<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title>You have a new support message</title>
    <style type="text/css">
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #eee;
            font-family: "Helvetica", sans-serif;
        }

        main {
            width: 720px;
            position: relative;
            left: 50%;
            margin: 30px 0 30px -360px;
            padding: 15px;
            box-shadow: 0 0 3px rgba(0,0,0,0.6);
            background-color: #fff;
        }

        h5 {
            font-size: 20px;
            padding: 0;
            margin: 30px 0 10px;
        }

        table {
            width: 100%;
        }

        table tr th,
        table tr td {
            padding: 10px 5px;
        }

        table thead tr th {
            border-top: solid 1px lightgrey;
            border-bottom: solid 1px lightgrey;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

<main role="main" class="container">
    <p class="text-center">
        Hello, {{ $user->name }} {{ $user->last_name }}
        <br />
        Today <b>{{ now()->format('d.m.Y') }}</b>,
        you have <b>{{ $notify->count() }}</b> unread notifications,
        as well as <b>{{ $tasks->count() }}</b> tasks
    </p>

    @if($notify->count() > 0)
    <h5>Notifications</h5>

    <table class="table">
        <thead>
        <tr>
            <th scope="col" class="text-center">Sender</th>
            <th scope="col" class="text-center">Message</th>
            <th scope="col" class="text-center">Date</th>
        </tr>
        </thead>
        <tbody>

        @foreach ($notify as $item)
            <tr>
                <td class="text-center">{{ $item->data['sender_name'] }} {{ $item->data['sender_last_name'] }}</td>
                <td class="text-center">{!! $item->data['message'] . (!empty($item->data['task_name']) ? (' `' . $item->data['task_name'] .'`') : '') !!}</td>
                <td class="text-center">{{ $item->created_at }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    @endif

    @if($tasks->count() > 0)
    <h5>Tasks</h5>

    <table class="table">
        <thead>
        <tr>
            <th scope="col" style="text-align: center">#</th>
            <th scope="col" class="text-center" style="text-align: center">Group Name</th>
            <th scope="col" class="text-center" style="text-align: center">Board Name</th>
            <th scope="col" class="text-center" style="text-align: center">Task Name</th>
            <th scope="col" class="text-center" style="text-align: center">Deadline</th>
        </tr>
        </thead>
        <tbody>

        @foreach ($tasks as $key => $task)
            <tr>
                <td class="text-center" style="text-align: center">{{ $key + 1 }}</td>
                <td class="text-center" style="text-align: center">{{ $task->group_name }}</td>
                <td class="text-center" style="text-align: center">{{ $task->board_name }}</td>
                <td class="text-center" style="text-align: center">{{ $task->name }}</td>
                <td class="text-center" style="text-align: center">{{ $task->deadline }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    @endif

    <p class="text-center">
        Please go to <a href="{{config('app.url')}}">{{config('app.name')}}</a> to check them!
    </p>
</main>

</body>
</html>
