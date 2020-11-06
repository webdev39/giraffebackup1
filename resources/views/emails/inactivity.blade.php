<p>Hello, {{ $user->name }}.</p>

@if ($remainder > 0)
    <p>
        You have not been active during {{ $absence }} days.
        Please, log in, otherwise you will be deleted from the system within {{ $remainder }} days.
    </p>
@else
    <p>
        You have not visited us for {{ $absence }} days, so we deleted all your data. Goodbye!
    </p>
@endif

