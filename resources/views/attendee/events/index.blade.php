@extends('layouts.auth.attendee')

@section('content')
    @php
        $color_status = [
            'PENDING' => 'text-primary',
            'ONGOING' => 'text-success',
            'CONCLUDED' => 'text-secondary'
        ]
    @endphp

    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <h1 class="text-secondary">MY EVENTS</h1>
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

            <div class="col-md-12">
                @if($attended_events->count())
                    @foreach ($attended_events as $event)

                        <div class="card mb-2">
                            <div class="card-header">
                                <h3>
                                    <a class="text-decoration-none {{ $color_status[$event->dynamic_status] }}" href="{{ route('events.show', [$event->code]) }}">{{ $event->name }}</a>

                                    @if ($event->is_confirmed)
                                        <i title="You have accepted the invitation to this event." class="float-right text-success fas fa-check-circle"></i>
                                    @endif
                                </h3>
                            </div>

                            <div class="card-body">

                                @include('partials.event_schedules')

                                <sub>type: {{ $event->type->name }}</sub>
                                <sub>category: {{ $event->category->name}}</sub>

                                @forelse ($event->uploaded_documents as $name => $path)
                                    @if ($loop->first)
                                        <hr>
                                        <br>
                                        <p>Uploaded Documents:</p>
                                    @endif
                                    <a title="click to download attached document" href="{{ route('helpers.download-event-attachment', ['document' => $path]) }}" target="_blank" class="pt-2 pb-2 mb-1 mt-1 badge badge-secondary">
                                        <i class="fas fa-download"></i> {{ $name }}
                                    </a>
                                @empty
                                    {{-- no attachments --}}
                                @endforelse

                                @php
                                    $has_evaluation = $event->has_evaluation;
                                    $has_concluded = $event->dynamic_status == 'CONCLUDED';
                                    $has_not_evaluated = $event->evaluations->where('attendee_id', Auth::user()->id)->count() == 0;
                                @endphp

                                @if($has_evaluation && $has_concluded && $has_not_evaluated)
                                    <div class="float-right">
                                        <a class="btn btn-primary" href="{{ route('attendee.events.evaluation', [$event->code]) }}">Evaluate</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @endforeach

                    {{-- <div class="d-flex justify-content-center">
                        {!! $attended_events->links() !!}
                    </div> --}}
                @else
                    <br>
                    <p class="text-center my-5">
                        You haven't been to any events!

                        <br>
                        <a class="text-center my-5" href="{{ route('events.index') }}">Browse Events</a>
                    </p>
                @endif
            </div>

        </div>
    </div>
@endsection
