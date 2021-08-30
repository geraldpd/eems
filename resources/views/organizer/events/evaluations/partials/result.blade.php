
<table id="table" class="table table-sm table-striped table-bordered">
    <thead>
        <th><small>Attendee Info</small></th>

        @foreach ($event->evaluation_questions_array as $index => $entry)
            @php
                $key = array_keys($entry)[0];
                $value = array_values($entry)[0];
            @endphp

            <th>
                <small>{{ $value }}</small>
            </th>
        @endforeach
        <th><small>Questions</small></th>
    </thead>

    <tbody>
        <tr>

            @forelse($event->evaluations as $evaluation)

            @empty
                <tr>
                    <td colspan="{{ count($event->evaluation_questions_array) + 2 }}">
                        <h3 class="text-center"> No evaluations yet</h3>
                    </td>
                </tr>
            @endforelse
    </tbody>

</table>