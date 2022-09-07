@extends('layouts.organizer')

@section('content')
@if(session()->has('message'))
<div class="alert alert-info">
    {{ session()->get('message') }}
</div>
@endif

<div id='calendar'></div>
@endsection

@push('modals')
<div class="modal fade" id="date-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title date-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="date-events">
                    <div class="event row container">
                        <div class="col-md-6">
                            <span>Location</span>
                            <h2>Name</h2>
                            <p>
                                <span>start</span> - <span>end</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>Category: <b>Category</b></p>
                            <p>Type: <b>type</b></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer modal-footer--sticky">
                <button type="button" class="btn btn-link pull-left toggle-concluded-events" >Toggle concluded events</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary add-event-button">Add Event</a>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<style>
    .event {
        background-color: #e6e6e642;
        margin-right: 5px;
        margin-left: 5px;
        margin-bottom: 10px;
        border-radius: 12px;
    }

    .fc-daygrid-event:hover{
        transform: scale(1.1);
        cursor: pointer;
    }

    .fc .fc-daygrid-day.fc-day-today {
        background: white !important;
        border: 3px solid #2c3e50;
    }

    @font-face {
        font-family: 'digital7';
        font-weight: normal;
        font-style: normal;
        src: url('{{asset('/fonts/custom/digital-7.ttf')}}') format('truetype');
    }

    .event-status-timer {
        font-family: 'digital7', sans-serif;
    }

    .add-events-button {
        display:none;
    }
    /* Footer fixed to the bottom of the modal */
    .modal-footer--sticky {
        position: sticky;
        bottom: 0;
        background-color: inherit; /* [1] */
        z-index: 1055; /* [2] */
    }
</style>
@endpush

@push('scripts')
<script type="text/javascript">
    const config = {
        routes: @json(routes('events', null, 'organizer')),
        events: @json($events)
    }

    config.routes.fetchScheduleEvents = '{{ route('organizer.events.fetch-scheduled-events') }}'
    config.routes.invitations = '{{ route('organizer.invitations.index', ['resource_id']) }}'
    config.routes.create = '{{ route('organizer.events.create') }}'
</script>
<script src="{{ asset('scripts/organizer/events/index.js') }}"></script>
@endpush