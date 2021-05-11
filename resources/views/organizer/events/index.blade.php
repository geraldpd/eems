@extends('layouts.organizer')

@section('content')
    <div class="container">

        @foreach($events as $event)
            {{ $loop->iteration }}
            <br>
        @endforeach

        <div id='calendar'></div>

    </div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.css' rel='stylesheet' />
@endpush

@push('scripts')

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.js'></script>
    <script type="text/javascript">
        $(function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    {
                        id: 'a',
                        title: 'my event',
                        start: '2021-05-11',
                        end: '2021-05-13'
                    }
                ],
                eventClick: function(info) {
                    alert('Event: ' + info.event.title);
                    alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
                    alert('View: ' + info.view.type);

                    console.log(info.el)
                }
            });
            calendar.render();
        })
    </script>
@endpush
