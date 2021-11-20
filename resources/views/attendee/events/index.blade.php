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
                                <h4>{{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}</h4>

                                {{ $event->type }}
                                {{ $event->category_name}}

                                @forelse ($event->uploaded_documents as $name => $path)
                                    @if ($loop->first)
                                        <hr>
                                        <br>
                                        <p>Uploaded Documents:</p>
                                    @endif
                                    <a title="click to download attached document" href="{{ $path['asset'] }}" download class="pt-2 pb-2 mb-1 mt-1 badge badge-secondary">
                                        <i class="fas fa-download"></i> {{ $name }}
                                    </a>
                                @empty
                                    {{-- no attachments --}}
                                @endforelse

                                @if($event->has_evaluation && $event->schedule_start->isPast() && !in_array(Auth::user()->id, $event->evaluated_attendees))
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
                    <div class="card">
                        <div class="card-header">You have not yet attended nor invited to any events</div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
