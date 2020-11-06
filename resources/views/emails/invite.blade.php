<p>{{ $user->name . ' ' . $user->last_name }} confirm your invite in team {{ $tenant->company_name ?? $tenant->project_name }}.</p>

@if(!isset($password))
    <a href="{{ url('/invite?confirm_hash=' . $user->confirm_hash . '&invite_hash=' . $userTenant->invite_hash) }}">Push here to JOIN!!</a>
@else
    <a href="{{ url('/login?confirm_hash=null') }}">{{ env('APP_NAME') }}</a>
    <p>Login: <b>{{$user->email}}</b></p>
    <p>Password: <b>{{$password}}</b></p>
@endif

