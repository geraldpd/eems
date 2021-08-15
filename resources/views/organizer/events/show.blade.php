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
            <div class="col-md-12">
                <div class="float-right">
                    @if(!$event->schedule_start->isPast())
                        <a href="{{ route('organizer.events.edit', [$event->code]) }}" class="btn btn-link">Edit</a>
                    @endif
                    <a href="{{ route('organizer.invitations.index', [$event->code]) }}" class="btn btn-link">Invitations</a>
                </div>
            </div>

            <div class="col-md-12"><br></div>

            <div class="col-md-3">
                    <img class="mx-auto d-block" src="{{ asset($event->qrcode) }}" alt="Event Qrcode" style="height: 200px;">
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