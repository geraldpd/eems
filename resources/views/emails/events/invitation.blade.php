@component('mail::message')

# You have been invited to attend {{ $event->name }}

### {{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}

<br>
{{ $event->location }}

Category: <strong>{{ $event->category->name }}</strong> | Type: <strong>{{ $event->type->name }}</strong>

{!! $event->description !!}

@component('mail::button', ['url' => $invitation_link])
    confirm invitation
@endcomponent

<br>

<img class="mx-auto d-block" src="{{ asset($event->qrcode) }}" alt="{{ route('events.show', $event->code).'?invite=true' }}" style="width: 30%;">
<br>
<small class="mx-auto d-block" style="width: 30%;"> Share this qrcode to your friends to invite them too.</small>

Thanks,<br>
{{ $event->organizer->firstname }} {{ $event->organizer->lastname }}
<br>
<small>{{ $event->organizer->email }}</small>
@endcomponent