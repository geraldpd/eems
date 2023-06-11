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

        <h1 class="text-secondary">MY INVITATIONS</h1>
        <div class="row justify-content-center">

            <div class="col-md-12">
                @if($invited_events->count())
                    @foreach ($invited_events as $event)

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
                                    <a title="click to download attached document" href="{{ route('helpers.download-file', ['document' => $path]) }}" target="_blank" class="pt-2 pb-2 mb-1 mt-1 badge badge-secondary">
                                        <i class="fas fa-download"></i> {{ $name }}
                                    </a>
                                @empty
                                    {{-- no attachments --}}
                                @endforelse

                                @if($event->has_evaluation && $event->dynamic_status == 'CONCLUDED' && !in_array(Auth::user()->id, $event->evaluated_attendees))
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
                        No Invitations yet

                        <br>
                        <a class="text-center my-5" href="{{ route('events.index') }}">Browse Events</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
