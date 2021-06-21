@extends('layouts.organizer')

@section('content')
    <div class="container">

        <div class="float-right">
            <a href="{{ route('organizer.events.index') }}"" class="btn btn-link">Events</a>
            @if(!$event->schedule_start->isPast())
                <a href="{{ route('organizer.events.edit', [$event->id]) }}" class="btn btn-link">Edit Event</a>
            @endif
        </div>

        <br>

        <h1>{{ $event->name }}</h1>
    </div>
@endsection

@push('scripts')
  <script src="{{ asset('scripts/organizer/events/show.js') }}"></script>
@endpush