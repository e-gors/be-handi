@component('mail::message')
Hi {{ env('APP_NAME') }},

{{ $request->letter }}


Thanks,<br>
{{ $request->email }}
@endcomponent