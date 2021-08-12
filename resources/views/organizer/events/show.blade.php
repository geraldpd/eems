@extends('layouts.organizer')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-right">
                    @if(!$event->schedule_start->isPast())
                        <a href="{{ route('organizer.events.edit', [$event->code]) }}" class="btn btn-link">Edit</a>
                    @endif
                    <a href="{{ route('organizer.invitations.index', [$event->code]) }}" class="btn btn-link">Invitations</a>
                    <a href="{{ route('organizer.events.index') }}"" class="btn btn-link">Events</a>
                </div>
            </div>

            <div class="col-md-3">
                <img src="{{ asset($event->qrcode) }}" alt="Event Qrcode" style="height: 200px;">
            </div>
            <div class="col-md-9">
                share this QR code to directly invite them to this event
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