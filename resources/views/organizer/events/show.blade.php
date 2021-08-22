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
                @if(!$event->schedule_start->isPast())
                    <a href="{{ route('organizer.events.edit', [$event->code]) }}" class="btn btn-link">Edit</a>
                @endif

                <a href="{{ route('organizer.invitations.index', [$event->code]) }}" class="btn btn-link">
                    Invitations
                    @switch(true)
                        @case(!$event->invitations->count() && !$event->schedule_start->isPast()) {{-- when there is no one invited yet and has not yet started--}}
                            <span class="badge badge-primary" title="Invite attendees to your event" >
                                <i class="fas fa-user-plus"></i>
                            </span>
                            @break
                        @case($event->notif_confirmed_attendee_count && !$event->schedule_end->isPast()) {{-- when there is no one invited yet and has not yet started --}}
                            <span class="badge badge-primary" title="{{ $event->notif_confirmed_attendee_count }} new confirmed attendees">
                               {{ $event->notif_confirmed_attendee_count }}
                            </span>
                            @break
                        @default

                    @endswitch
                </a>
                <a href="#" class="btn btn-link">
                    Evaluations
                    <span class="badge badge-primary">
                        @if (!$event->evaluation_id && !$event->schedule_start->isPast())  {{-- when there is no set evaluation sheet, and has not yet started--}}
                            <i title="Provide and evaluation sheet to this event" class="fas fa-clipboard-list"></i>
                        @endif
                    </span>
                </a>
            </div>

            <div class="col-md-12"><br></div>

            <div class="col-md-3">
                <img class="mx-auto d-block" src="{{ asset($event->qrcode) }}" alt="{{ route('events.show', $event->code).'?invite=true' }}" style="width: 100%;">
            </div>

            <div class="col-md-9">
                <br>
                <h3>Share this QR code to directly invite them to this event</h3>
                <p>Users will need to signup(for unregistered) and login to their EEMS account <br> to be automatically booked to this event.</p>
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