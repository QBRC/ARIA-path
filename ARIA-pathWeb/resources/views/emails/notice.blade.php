@component('mail::message')
# New registration

{{ $user->name }} ({{ $user->email }}) registered at {{ $user->created_at }}.
Please review new user's profile and decide whether to activate the account.

@component('mail::button', ['url' => url('/users/')])
{{--    @component('mail::button', ['url' => url('/users//').$user->id])--}}
View new user
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
