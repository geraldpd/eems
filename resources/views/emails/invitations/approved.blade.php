@component('mail::message')

# Congratulations, You have been approve to attend {{ $event->name }}

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


{{-- <div class="p-3 mb-2 bg-light">
    {!! $event->description !!}
</div> --}}

{{ $event->location }}

@component('mail::button', ['url' => route('events.show', $event->code)])
check event
@endcomponent

<br>

<img class="mx-auto d-block" src="{{ asset($event->qrcode_path) }}" alt="{{ route('events.show', $event->code).'?invite=true' }}" style="width: 30%;">
<br>
<small class="mx-auto d-block" style="width: 30%;"> Share this qrcode to your friends to invite them too.</small>

Thanks,<br>
{{ $event->organizer->firstname }} {{ $event->organizer->lastname }}
<br>
<small>{{ $event->organizer->email }}</small>
@endcomponent