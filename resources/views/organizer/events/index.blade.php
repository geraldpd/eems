@extends('layouts.organizer')

@section('content')
    <div class="container">

        @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
        @endif

        <div id='calendar'></div>

    </div>
@endsection

@push('modals')
    <div class="modal fade" id="date-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title date-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="date-events"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
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