@extends('layouts.app')

@section('content')

<div class="container">
    <h1>BROWSE EVENTS</h1>
    <div class="row justify-content-center">

        <div class="col-md-12">
            <form action="" method="GET">
                <div class="input-group mb-3">
                    @csrf
                    <input type="text" class="form-control form-control-lg" name="keyword" placeholder="Search for events" aria-label="Search for events" value="{{ old('keyword') ? old('keyword') : request()->keyword}}">
                    <div class="input-group-append">
                        <button class="input-group-text" type="submit">Search</button>
                    </div>
                </div>
            </form>
        </div>

        @if($events->count())

            @foreach ($events as $event)
                <div class="col-md-12">
                    <a href="{{ route('events.show', [$event->code]) }}" class="text-decoration-none text-secondary">
                        <div class="card">
                            <div class="card-header">
                                @auth
                                    @if ($event->attendees->pluck('id')->contains(Auth::user()->id))

                                        @switch(eventHelperGetDynamicStatus($event))
                                            @case('PENDING')
                                            @case('ONGOING')
                                                <i class="float-right text-success fas fa-check-circle" title="you will attend this event"></i>
                                                @break
                                            @default {{-- CONCLUDED --}}
                                                <i class="float-right text-success fas fa-check-circle" title="you have attended this event"></i>
                                        @endswitch

                                    @endif
                                @endauth
                                <h2 class="text-dark">
                                    {{ $event->name }}
                                </h2>
                            </div>

                            <div class="card-body">
                                @include('partials.event_schedules')

                                <div class="description-div">
                                    {!! $event->description !!}
                                </div>

                                <a href="{{ route('events.show', [$event->code]) }}">...Read More</a>

                            </div>
                        </div>
                    </a>
                    <br>
                </div>
            @endforeach

            <div class="d-flex justify-content-center">
                {!! $events->links() !!}
            </div>
        @else
            No event yet!
        @endif
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