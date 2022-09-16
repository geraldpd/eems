@extends('layouts.admin')

@section('content')
    <div class="container">

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ ucwords(strtolower($event->name)) }}</li>
        </ol>

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <h1>{{$event->name }}</h1>
            </div>

            <div class="col-md-12"> <br> </div>

            <div class="col-md-3">
                <img class="mx-auto d-block" src="{{ asset($event->qrcode) }}" alt="{{ route('events.show', $event->code).'?invite=true' }}" style="width: 100%;">
                <br>
            </div>

            <div class="col-md-9">

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

                <br>

                @include('partials.event_schedules')

                <div class="p-3 mb-2 bg-light">
                    {!! $event->description !!}
                </div>

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