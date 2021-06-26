@component('mail::subcopy', ['url' => $url])
@endcomponent

@component('mail::message')

# You have been invited to attend {{ $event->name }}

### {{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}

<br>
{{ $event->location }}

Category: <strong>{{ $event->category->name }}</strong> | Type: <strong>{{ $event->type }}</strong>

{!! $event->description !!}

@component('mail::button', ['url' => $url])
    confirm invitation
@endcomponent

Thanks,<br>
{{ $event->organizer->firstname }} {{ $event->organizer->lastname }}
<br>
<small>{{ $event->organizer->email }}</small>
@endcomponent