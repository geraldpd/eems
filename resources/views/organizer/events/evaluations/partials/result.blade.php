
@php
    $questions = $event->evaluation_questions_array->keys()->all();
@endphp

<table id="table" class="table table-sm table-striped table-bordered">
    <thead>
        <th><small>Attendee Info</small></th>
        @foreach ($event->evaluation_questions_array as $name => $question)
            <th>
                <small>{{ $question }}</small>
            </th>
        @endforeach
    </thead>

    <tbody>


        @forelse($event->evaluations as $evaluation)
            <tr>
                <td>
                    <strong>{{ $evaluation->attendee->fullname }}</strong>
                    <br>
                    {{ $evaluation->attendee->email }}
                </td>

                @foreach ($questions as $question)
                    <td class="text-center">
                        @if(array_key_exists($question, $evaluation->feedback))

                            @php $feedback = $evaluation->feedback[$question] @endphp

                            @if (gettype($feedback) === 'string')
                                {{ $feedback }}
                            @else
                                {{ collect($feedback)->join(', ') }}
                            @endif

                        @endif
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ $event->evaluation_questions_array->count() + 2 }}">
                    <h3 class="text-center"> No evaluations yet</h3>
                </td>
            </tr>
        @endforelse

    </tbody>

</table>