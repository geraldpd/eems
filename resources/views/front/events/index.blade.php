@extends('layouts.app')

@section('content')

<div class="container">
    <h1>BROWSE EVENTS</h1>
    <div class="row justify-content-center">
        @forelse ($events as $event)
            <div class="col-md-12">
                <a href="{{ route('events.show', [$event->code]) }}" class="text-decoration-none text-secondary">
                    <div class="card">
                        <div class="card-header">
                            @if ($event->attendees->pluck('id')->contains(Auth::user()->roles()->first()->id))
                                <i class="float-right text-success fas fa-check-circle" title="you have attended this event"></i>
                            @endif
                            <h2 class="text-dark">
                                {{ $event->name }}
                            </h2>
                        </div>

                        <div class="card-body">
                            <h4>{{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}</h4>

                            <div class="description-div">
                                {!! $event->description !!}
                            </div>

                            <a href="{{ route('events.show', [$event->code]) }}">...Read More</a>

                        </div>
                    </div>
                </a>
                <br>
            </div>
        @empty
            No Event
        @endforelse
    </div>
</div>

@endsection

@push('styles')
    <style>
        .description-div {
            max-height: 350px;
            overflow: hidden;
        }

    </style>
@endpush