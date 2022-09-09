@extends('layouts.organizer')

@section('content')
    <div class="container">

        <ol class="breadcrumb d-print-none">
            <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('organizer.events.show', [$event->code]) }}">{{ ucwords(strtolower($event->name)) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Evaluations</li>
        </ol>

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="row">

            {{-- @include('organizer.events.evaluations.partials.preview') --}}

            @if($event->has_evaluation)
                @switch($event->dynamic_status)
                    @case('CONCLUDED')
                        <h1 class="text-secondary">No evaluation sheet has been applied to this event.</h1>
                        <p>No results to show</p>
                    @break

                    @case('PENDING')
                    @case('ONGOING')

                        <div class="col-md-12">
                            <div class="jumbotron">
                                <span>This event will be using:</span>
                                <h1 class="display-4">{{ ucwords($event->evaluation_name) }}</h1>

                                <p class="lead">{{ $event->evaluation_description }}</p>

                                <hr class="my-4">

                                <div class="row">
                                    <div class="col-md-6">
                                        {{ $event->evaluation_questions_array ? count($event->evaluation_questions_array).' Entries' : 'No questions set' }}
                                    </div>

                                    <div class="col-md-6">
                                        <div class="float-right">
                                            <form id="remove-evaluation-sheet-form" action="{{ route('organizer.events.evaluations.destroy', [$event->code, $event->evaluation_id]) }}" method="post">
                                                @csrf
                                                @method('DELETE')

                                                <a class="btn btn-link" href="{{ route('organizer.evaluations.edit', [$event->evaluation_id, 'event' => $event->code]) }}">Modify evaluation Entries</a>

                                                <a class="btn btn-link" href="{{ route('organizer.evaluations.index', ['event' => $event->code]) }}">Reuse another sheet</a>

                                                <button type="button" class="btn btn-link text-secondary remove-evaluation-sheet" href="">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @break
                @endswitch

                <div class="col-md-12">
                    <form action="{{ route('organizer.events.evalautions.close-open', [$event]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn {{ $event->evaluation_is_released ? 'btn-primary' : 'btn-secondary' }} evaluation_is_released">
                            @if($event->evaluation_is_released)
                                <i class="fas fa-lock-open"></i> Open for Evaluation
                            @else
                                <i class="fas fa-lock"></i> Closed for Evaluation
                            @endif
                        </button>
                    </form>
                </div>

            @else
                <div class="col-md-6">

                    @switch($event->dynamic_status)
                        @case('CONCLUDED')
                            <h1 class="text-secondary">No evaluation sheet has been applied to this event.</h1>
                            <p>No results to show</p>
                        @break

                        @case('PENDING')
                        @case('ONGOING')
                            <h1 class="text-secondary">You don't seem to have set any evaluation sheet for this event.</h1>
                            <p>Why dont you give it an evaluation sheet so you can get the attendees feedback.</p>

                            <br>

                            <a href="{{ route('organizer.evaluations.create', ['event' => $event->code]) }}"  class="btn btn-light">
                                <i class="fas fa-plus-square"></i> Create a new evaluation sheet
                            </a>

                            <a href="{{ route('organizer.evaluations.index', ['event' => $event->code]) }}"  class="btn btn-light">
                                <i class="fas fa-recycle"></i> Reuse existing evaluation sheet
                            </a>
                        @break
                    @endswitch

                </div>
            @endif

            @if(!$event->invitations->count())
                <div class="col-md-12">
                    <p>Dont forget to <a href="{{ route('organizer.invitations.index', [$event->code]) }}"> invite attendees</a> </p>
                </div>
            @endif
        </div>

    </div>
@endsection

@push('modals')
@endpush

@push('styles')
@endpush

@push('scripts')
    <script>
        $(function() {

            const evaluation_is_released = {!! $event->evaluation_is_released !!};
            const open_evaluation = _ => _.addClass('btn-primary').removeClass('btn-secondary').html('<i class="fas fa-lock-open"></i> Open for Evaluation');
            const close_evaluation = _ => _.addClass('btn-secondary').removeClass('btn-primary').html('<i class="fas fa-lock"></i> Close for Evaluation');

            $('.evaluation_is_released')
            .mouseover(function() {
                evaluation_is_released ? close_evaluation($(this)) : open_evaluation($(this));
            })
            .mouseleave(function() {
                evaluation_is_released ? open_evaluation($(this)) : close_evaluation($(this));
            });

            $('.remove-evaluation-sheet').on('click', _ => {
                window.Swal.fire({
                    title: `Remove Evaluation Sheet?`,
                    text: 'You wont be able to get your attendees feedback when you remove this evaluation sheet, proceed anyway?',
                    icon: 'question',
                    confirmButtonText: 'Yes',
                    confirmButtonColor: '#007bff',
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        $('#remove-evaluation-sheet-form').submit()
                    }
                });

            });

            $('.print-button').on('click', _ => {
                window.print()
            });
        })
    </script>
@endpush