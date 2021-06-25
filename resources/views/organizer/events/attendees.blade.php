@extends('layouts.organizer')

@section('content')
    <div class="container">
        <div class="row float-right">
            @if(!$event->schedule_start->isPast())
            <a href="{{ route('organizer.events.edit', [$event->id]) }}" class="btn btn-link">Edit</a>
            @endif
            <a href="{{ route('organizer.events.index') }}"" class="btn btn-link">Events</a>
            <a href="{{ route('organizer.events.show', [$event->id]) }}" class="btn btn-link">Preview</a>
        </div>
    </div>

    <br>
    <br>

    <div class="container">

        <h1>{{ $event->name }}</h1>

        <form method="POST" action="{{ route('organizer.events.update', [$event->id]) }}">
            @method('PUT')
            @csrf

            <div class="input-group mb-3">
                <input type="text" name="email" id="email" class="form-control" placeholder="Invite people to your event!" aria-label="Search by email or name">
                <div class="input-group-append">
                  <button class="btn btn-primary add-attendee" type="button">Invite</button>
                </div>
              </div>
        </form>

        <ol id="attendees-list"></ol>

        <table class="table table-bordered table-condensed">
            <thead>
                <td class="text-center">Email</td>
                <td class="text-center">Invitaion Sent</td>
                <td class="text-center">Confirmed Attendance</td>
            </thead>
            <tbody>
                @forelse ($event->attendees as $attendee)
                    <tr>
                        <td class="text-center"> {{ $attendee->email }} </td>
                        <td class="text-center"> {{ $attendee->pivot->id }} </td>
                        <td class="text-center"> {{ $attendee->pivot->is_confirmed }} </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="3"> No Attendees </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
@endsection

@push('scripts')
  <script src="{{ asset('scripts/organizer/events/attendees.js') }}"></script>
@endpush