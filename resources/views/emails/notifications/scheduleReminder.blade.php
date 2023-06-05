@component('mail::message')
# Introduction

This is a message notification for schedule.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent