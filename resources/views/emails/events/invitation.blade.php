@component('mail::message')

# You have been invited to attend {{ $event->name }}

<br>
Category: <strong>{{ $event->category->name }}</strong>
<br>
Type: <strong>{{ $event->type->name }}</strong>
<br>
<br>

Schedules:
<table class="table">
    <tbody>
    @foreach ($event->schedules as $schedule)
        @php
            $schedule_day = $schedule->schedule_start->isoFormat('MMM D Y, dddd')
        @endphp
        <tr>
            <td>{{ $schedule_day }}</td>
            <td>{{ $schedule->schedule_start->isoFormat('H:mm A') }} - {{ $schedule->schedule_end->isoFormat('H:mm A') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

{!! $event->description !!}

<br>
{{ $event->location }}

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