<div>
    <h4> Schedules</h4>
    <table class="table">
        <tbody>
        @foreach ($event->schedules as $schedule)
            @php
                $schedule_day = $schedule->schedule_start->isoFormat('MMM D Y, dddd')
            @endphp
            <tr>
                <td>{{ $schedule_day }}</td>
                <td>{{ $schedule->schedule_start->isoFormat('H:mm A') }} - {{ $schedule->schedule_end->isoFormat('H:mm A') }}</td>
                <td>
                    @switch(true)
                        @case($schedule->status == 'ongoing')
                            <i class="fas fa-circle"></i>
                            @break
                        @case($schedule->status == 'concluded')
                            <i class="fas fa-check"></i>
                            @break
                        @default
                            {{ $schedule->status }}
                            @break
                    @endswitch
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>