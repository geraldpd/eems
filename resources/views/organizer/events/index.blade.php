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
    <script src="{{ asset('scripts/organizer/events/index.js') }}"></script>
@endpush
