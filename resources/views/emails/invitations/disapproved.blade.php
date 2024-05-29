@component('mail::message')

# Apologies, Your invitation to attend {{ $event->name }} has been disapproved.

<br>

<span>
Reason: <strong style="color: rgb(219, 120, 120)">{!! $reason !!}</strong>
</span>

<br>

<br>
Category: <strong>{{ $event->category->name }}</strong>
<br>
Type: <strong>{{ $event->type->name }}</strong>

@component('mail::table')
    | SCHEDULES    |               |
    | ------------- |:-------------:|
    @foreach ($event->schedules as $schedule)
        @php
            $schedule_day = $schedule->schedule_start->isoFormat('MMM D Y, dddd')
        @endphp
        | {{ $schedule_day }}    | {{ $schedule->schedule_start->isoFormat('H:mm A') }} - {{ $schedule->schedule_end->isoFormat('H:mm A') }}    |
    @endforeach
@endcomponent

{{ $event->location }}

<br>

Thanks,<br>
{{ $event->organizer->firstname }} {{ $event->organizer->lastname }}
<br>
<small>{{ $event->organizer->email }}</small>
@endcomponent