@extends('layouts.organizer')

@section('content')
    <h1> {{ ucfirst($filter) }} {{ $event->name }} Attendees</h1>
    <p>Print Date: {{ date('M d Y') }}</p>
    </div>

    <table id="table" class="table table-bordered">
        <thead class="none">
            <th style="display:none">created_at</th> <!-- just for ordering -->
            <th class="text-center">Response</th>
            <th>Email</th>
            <th>Name</th>
            <th>Organization</th>
        </thead>
        <tbody>
            @forelse ($participants as $participant)
                <tr>
                    <td style="display:none">{{ $participant['created_at'] }}</td> <!-- just for ordering -->
                    <td class="text-center">{{ $participant['response'] }}</td>
                    <td>{{ $participant['email'] }}</td>
                    <td>{{ $participant['name'] }}</td>
                    <td>{{ $participant['organization'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No guest invited yet</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

@push('scripts')
    <script type="text/javascript">
         window.onafterprint = window.close;
         window.print();
    </script>
@endpush
