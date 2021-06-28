@extends('layouts.organizer')

@section('content')
    <div class="container">
        <div class="float-right">
            @if(!$event->schedule_start->isPast())
                <a href="{{ route('organizer.events.edit', [$event->code]) }}" class="btn btn-link">Edit</a>
            @endif
            <a href="{{ route('organizer.invitations.index', [$event->code]) }}" class="btn btn-link">Invitations</a>
            <a href="{{ route('organizer.events.index') }}"" class="btn btn-link">Events</a>
        </div>
    </div>

    <br><br><br>

    {!! $preview !!}
@endsection


@push('scripts')
  <script src="{{ asset('scripts/organizer/events/show.js') }}"></script>
@endpush