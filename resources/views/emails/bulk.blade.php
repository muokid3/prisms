@component('mail::message')
# Greetings!

{{--@component('mail::panel')--}}
{{--{{$bulkMail->subject}}--}}
{{--@endcomponent--}}

{!! $bulkMail->content !!}

@component('mail::button', ['url' => url('/')])
Login to PRISMS
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
