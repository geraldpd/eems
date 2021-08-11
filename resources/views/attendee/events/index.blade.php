@extends('layouts.app')

@section('content')
<div class="container">
    <h1>MY EVENTS</h1>
    <div class="row justify-content-center">

        @forelse ($attended_events as $event)
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>
                            <a href="{{ route('events.show', [$event->code]) }}">{{ $event->name }}</a>
                        </h2>
                    </div>

                    <div class="card-body">
                        <h4>{{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}</h4>

                        <p>{{ $event->description }}</p>
                    </div>
                </div>
                <br>
            </div>
        @empty
            <div class="card">
                <div class="card-header">NO EVENT FOUND</div>
            </div>
        @endforelse

    </div>
</div>
@endsection
