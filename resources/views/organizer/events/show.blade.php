@extends('layouts.organizer')

@section('content')
    <div class="container">

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ ucwords(strtolower($event->name)) }}</li>
        </ol>

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <h1>{{$event->name }}</h1>
            </div>

            <div class="col-md-4">
                {{-- @if(!$event->schedule_start->isPast()) --}}
                @if(!false)
                    <a href="{{ route('organizer.events.edit', [$event->code]) }}" class="btn btn-link">Edit</a>
                @endif

                {{-- <a href="{{ route('organizer.invitations.index', [$event->code, $event->schedule_start->isPast() ? 'confirmed' : '']) }}" class="btn btn-link"> --}}
                <a href="{{ route('organizer.invitations.index', [$event->code, false ? 'confirmed' : '']) }}" class="btn btn-link">
                    Invitations
                    @switch(true)
                        {{-- @case(!$event->invitations->count() && !$event->schedule_start->isPast()) when there is no one invited yet and has not yet started --}}
                        @case(!$event->invitations->count() && !false) {{-- when there is no one invited yet and has not yet started--}}
                            <span class="badge badge-primary" title="Invite attendees to your event" >
                                <i class="fas fa-user-plus"></i>
                            </span>
                            @break
                        {{-- @case($event->notif_confirmed_attendee_count && !$event->schedule_end->isPast()) when there is no one invited yet and has not yet started --}}
                        @case($event->notif_confirmed_attendee_count && !false) {{-- when there is no one invited yet and has not yet started --}}
                            <span class="badge badge-primary" title="{{ $event->notif_confirmed_attendee_count }} new confirmed attendees">
                               {{ $event->notif_confirmed_attendee_count }}
                            </span>
                            @break
                        @default

                    @endswitch
                </a>
                <a href="{{route('organizer.events.evaluations.index', [$event->code]) }}" class="btn btn-link">
                    Evaluations
                    {{-- @if (!$event->evaluation_id && !$event->schedule_start->isPast())  when there is no set evaluation sheet, and has not yet started --}}
                    @if (!$event->evaluation_id && !false)  {{-- when there is no set evaluation sheet, and has not yet started--}}
                        <span class="badge badge-primary">
                            <i title="Provide and evaluation sheet to this event" class="fas fa-clipboard-list"></i>
                        </span>
                    @endif
                </a>
            </div>

            <div class="col-md-12"> <br> </div>

            <div class="col-md-3">
                <img class="mx-auto d-block" src="{{ asset($event->qrcode) }}" alt="{{ route('events.show', $event->code).'?invite=true' }}" style="width: 100%;">
            </div>

            <div class="col-md-9">
                <br>
                <h3>Share this QR code to directly invite them to this event</h3>
                <p>Users will need to signup(for unregistered) and login to their {{ config('app.name') }} account <br> to be automatically booked to this event.</p>

                @forelse ($event->uploaded_documents as $name => $path)
                    @if ($loop->first)
                        <br>
                        <hr>
                        <p>Uploaded Documents:</p>
                    @endif
                    <a href="{{ $path['asset'] }}" target="_blank" class="pt-2 pb-2 mb-1 mt-1 badge badge-secondary">
                        {{ $name }}
                    </a>
                    @if ($loop->last)
                        <br>
                        <br>
                        <p>Uploaded documents will only be available for the events attendees.</p>
                    @endif
                @empty
                @endforelse

                <div>
                    <h3> Schedules</h3>
                    <table class="table">
                        <tbody>
                        @foreach ($event->schedules as $schedule)
                            @php
                                $schedule_day = $schedule->schedule_start->isoFormat('MMM D Y, dddd')
                            @endphp
                            <tr>
                                <td>{{ $schedule_day }}</td>
                                <td>{{ $schedule->schedule_start->isoFormat('H:mm A') }} - {{ $schedule->schedule_end->isoFormat('H:mm A') }}</td>
                                <td>
                                    @switch(true)
                                        @case($schedule->status == 'ongoing')
                                            <i class="fas fa-circle"></i>
                                            @break
                                        @case($schedule->status == 'concluded')
                                            <i class="fas fa-check"></i>
                                            @break
                                        @default
                                            {{ $schedule->status }}
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <br><br><br>

    {!! $preview !!}
@endsection

@push('styles')
    <style>
        .button {
            pointer-events: none;
        }
    </style>
@endpush

@push('scripts')
  {{-- <script src="{{ asset('scripts/organizer/events/show.js') }}"></script> --}}
  <script>
      $(function() {
        $('.button').attr('href', '#');
      });
  </script>
@endpush