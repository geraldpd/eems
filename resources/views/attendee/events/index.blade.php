@extends('layouts.auth.attendee')

@section('content')
<div class="container">
    <h1>MY EVENTS</h1>
    <div class="row justify-content-center">

        <div class="col-md-12">
            @forelse ($attended_events as $event)
                <div class="card" style="margin-bottom: 10px">
                    <div class="card-header">
                        <h2>
                            <a href="{{ route('events.show', [$event->code]) }}">{{ $event->name }}</a>

                            @if ($event->is_confirmed)
                                <i class="float-right text-success fas fa-check-circle"></i>
                            @endif
                        </h2>
                    </div>

                    <div class="card-body">
                        <h4>{{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}</h4>

                        <p>{{ $event->description }}</p>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-header">You have not yet attended nor invited to any events</div>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
