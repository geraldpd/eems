@extends('layouts.organizer')

@section('content')
    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <div id='calendar'></div>

    </div>
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
                        <div class="event row">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" class="btn btn-primary add-event-button">Add Event</a>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.css' rel='stylesheet' />
@endpush

@push('scripts')
    <script type="text/javascript">
        const config = {
            routes: @json(routes('events', null, 'organizer')),
            events: @json($events)
        }
    </script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.js'></script>
    <script src="{{ asset('plugins/moment.js') }}"></script>
    <script src="{{ asset('scripts/organizer/events/index.js') }}"></script>
@endpush