user -> {{ $user->name . ' ' . $user->last_name }} confirm your password restoring.

<a href="{{ url('/restore-password?resetToken='. $resetToken) }}">Push here to confirm restoring!!</a>