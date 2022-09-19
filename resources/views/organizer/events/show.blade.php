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

                @if(! in_array($event->schedules->last()->status, ['ONGOING', 'CONCLUDED']))
                    <a href="{{ route('organizer.events.edit', [$event->code]) }}" class="btn btn-link">Edit</a>
                @endif

                <a href="{{ route('organizer.invitations.index', [$event->code, $event->start->schedule_start->isPast() ? 'confirmed' : '']) }}" class="btn btn-link">
                    Invitations
                    @switch(true)
                        @case(!$event->invitations->count() && $event->dynamic_status != 'CONCLUDED')
                            {{-- when there is no one invited yet and has not yet started --}}
                            <span class="badge badge-primary" title="Invite attendees to your event" >
                                <i class="fas fa-user-plus"></i>
                            </span>
                            @break
                        @case($event->notif_confirmed_attendee_count && $event->dynamic_status != 'CONCLUDED')
                            {{-- when there is no one invited yet and has not yet started --}}
                            <span class="badge badge-primary" title="{{ $event->notif_confirmed_attendee_count }} new confirmed attendees">
                               {{ $event->notif_confirmed_attendee_count }}
                            </span>
                            @break
                        @default

                    @endswitch
                </a>
                <a href="{{route('organizer.events.evaluations.index', [$event->code]) }}" class="btn btn-link">
                    Evaluations
                    @if (!$event->evaluation_id && !$event->start->schedule_start->isPast())
                    {{-- when there is no set evaluation sheet, and has not yet started--}}
                        <span class="badge badge-primary">
                            <i title="Provide and evaluation sheet to this event" class="fas fa-clipboard-list"></i>
                        </span>
                    @endif
                </a>
            </div>

            <div class="col-md-12"> <br> </div>

            <div class="col-md-3">
                <img class="mx-auto d-block" src="{{ asset($event->qrcode_path) }}" alt="{{ route('events.show', $event->code).'?invite=true' }}" style="width: 100%;">
            </div>

            <div class="col-md-9">

                @if($event->dynamic_status != 'CONCLUDED')
                    <br>
                    <h3>Share this QR code to directly invite them to this event</h3>
                    <sub>Users will need to signup(for unregistered) and login to their {{ config('app.name') }} account <br> to be automatically booked to this event.</sub>
                    <br>
                @endif

                <br>

                <div class="row">
                    <div class="col-md-6">
                        Type: <strong>{{ $event->type->name }}</strong>
                        <br>
                        Category: <strong>{{ $event->category->name }}</strong>
                    </div>

                    <div class="col-md-6">
                        Attended:
                        <strong>
                            @if($event->dynamic_status == 'CONCLUDED')
                                {{ $event->attendees->count() }} users <span title="attendance percentage">({{ $event->attendance_percentage }}%)</span>
                            @else
                                TBD
                            @endif
                        </strong>
                        <br>
                        Evaluation:
                        <strong>
                            @if($event->has_evaluation)
                                {{ $event->evaluations->count() }} <span title="feedback percentage">({{ $event->feedback_percentage }}%)</span>
                            @else
                                N/A
                            @endif
                        </strong>
                    </div>

                </div>

                @forelse ($event->uploaded_documents as $name => $path)
                    @if ($loop->first)
                        <br>
                        <hr>
                        <h4>Uploaded Documents:</h4>
                    @endif
                    <a href="{{ route('helpers.download-event-attachment', ['document' => $path]) }}" target="_blank" class="pt-2 pb-2 mb-1 mt-1 badge badge-secondary">
                        {{ $name }}
                    </a>
                    @if ($loop->last)
                    <br>
                        <sub>Uploaded documents will only be available for the events attendees.</sub>
                        <br>
                    @endif
                @empty
                @endforelse

                <br>

                @include('partials.event_schedules')

            </div>
        </div>
    </div>

    <p class="text-center my-5">
        <button class="btn btn-link btn-light" onClick="$('#email-preview').toggle()">
            Email Preview
        </button>
    </p>

    <div id="email-preview" style="display:none" class="my-5">
        {!! $preview !!}
    </div>
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