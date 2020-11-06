user -> {{ $user->name . ' ' . $user->last_name }} Confirm your registration.

<a href="{{ url('/confirm?confirm_hash=' .$user->confirm_hash) }}">Push here to confirm!!</a>