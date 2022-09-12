@extends('layouts.organizer')

@section('content')

<div class="container">

    @if(session()->has('message'))
    <div class="alert alert-info">
        {{ session()->get('message') }}
    </div>
    @endif

    @if($event)
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
        <li class="breadcrumb-item"><a href="{{ route('organizer.events.evaluations.index', [$event->code]) }}">{{ $event->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reuse Evaluation sheet</li>
    </ol>
    @endif

    <div class="row">
        <div class="col-md-9">
            @if ($event)
            <h1 class="text-secondary"> Evaluation sheet for <a href="{{ route('organizer.events.evaluations.index', [$event->code]) }}">{{ $event->name }}</a> </h1>
            <p>Select an evaluation sheet to reuse for your event</p>
            @endif
        </div>
        <div class="col-md-3">
            @if($event)
            <a class="btn btn-primary float-right" href="{{ route('organizer.evaluations.create', ['event' => $event->code]) }}">Add Evaluation Sheet</a>
            @else
            <a class="btn btn-primary float-right" href="{{ route('organizer.evaluations.create') }}">Add Evaluation Sheet</a>
            @endif
        </div>
    </div>

    <br>

    @forelse ($evaluations as $evaluation)
        <div class="jumbotron">
            <h1 class="display-4">{{ ucwords($evaluation->name) }}</h1>

            <p class="lead">{{ $evaluation->description }}</p>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6">
                    <p>{{ $evaluation->questions_array ? count($evaluation->questions_array).' Entries' : 'No questions set' }}</p>
                </div>

                <div class="col-md-6">
                    @if ($event)

                        @if($event->evaluation_id == $evaluation->id)
                            <button class="btn btn-light float-right" disabled>Selected Evaluation</button>
                        @else
                            <form action="{{ route('organizer.events.evaluations.update', [$event->code, $evaluation->id]) }}" method="post">
                                @csrf
                                @method('PUT')
                                <button {{ $evaluation->events_count ? '' : 'disabled title="No entries set"' }} type="submit" class="btn btn-primary float-right use-evaluation_sheet" href="">Use This Evaluation Sheet</button>
                            </form>
                        @endif

                    @else

                        <a class="btn btn-link float-right" href="{{ route('organizer.evaluations.edit', [$evaluation->id]) }}">update</a>

                        <form action="{{ route('organizer.evaluations.destroy', [$evaluation->id]) }}" method="post">
                            @csrf
                            @method('delete')
                            <button type="button" class="btn btn-link text-secondary float-right remove-evaluation_sheet" href="">remove</button>
                        </form>

                        @if ($evaluation->events_count)
                            <button type="button" class="btn btn-link float-right pending_events-evaluation_sheet" data-evaluation_id="{{ $evaluation->id }}" data-name="{{ $evaluation->name }}" data-toggle="modal" data-target="#events-modal">
                                Used by {{ $evaluation->events_count }} Event(s)
                            </button>
                        @endif

                        @endif
                </div>
            </div>
        </div>
    @empty
    <h1>You dont have any Evaluation Sheets!</h1>
    <p>Why dont you make one, simply click on the Add Evaluation Sheet button.</p>
    @endforelse
</div>

@endsection

@push('modals')
    <div class="modal fade" id="events-modal" tabindex="-1" role="dialog" aria-labelledby="eventsModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5><span class="evaluation-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="evaluation-list"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endpush

@push('styles')
@endpush

@push('scripts')
<script type="text/javascript">
    $('.remove-evaluation_sheet').on('click', function(e) {

        window.Swal.fire({
            title: 'Delete this Evaluation Sheet?',
            text: 'Are you sure you want delete this evaluation sheet?',
            icon: 'question',
            confirmButtonText: 'Remove',
            confirmButtonColor: '#007bff',
            showCancelButton: true
        })
        .then((result) => {
            if(result.isConfirmed) {
                $(this).closest('form').submit();
            }
        });

    })

    $('.pending_events-evaluation_sheet').on('click', function() {

        $('.evaluation-name').text($(this).data('name'))

        let pending_events_url = "{{ route('organizer.evaluations.pending-events', ['evaluation' => 'evaluation_id']) }}".replace('evaluation_id', $(this).data('evaluation_id'))
        let event_url = event_code => "{{route('organizer.events.evaluations.index', ['event_code']) }}".replace('event_code', event_code)
        axios.get(pending_events_url)
        .then(function (response) {
            let pendingEvents = $.map(response.data, (event) => {
                event.name
                let schedules = $.map(event.schedules, schedule => `<li>${schedule.schedule_start} - ${schedule.schedule_end}</li>`).join('');
                return `<a href="${event_url(event.code)}">${event.name}</a><ul>${schedules}</ul>`
            }).join('<hr>');

            $('.evaluation-list').html(pendingEvents)
        })
        .catch(function (error) {
            console.log(error);
        });
    })
</script>
@endpush