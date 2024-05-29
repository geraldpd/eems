@extends('layouts.organizer')

@section('content')
    <div class="header">
        @if($event->organizer->organization->logo)
            <img src="{{ asset($event->organizer->organization->logo_path) }}" alt="logo" class="logo">
        @endif
        <div class="text">
            <h2>{{ $event->organizer->organization->name }}</h2>
            <h2>{{ $event->organizer->organization->department }}</h2>
            <h5>{{ $event->organizer->address }}</h5>
        </div>
    </div>

    <h2> {{ ucfirst($filter) }} {{ $event->name }} Attendees</h2>
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

@push('styles')
    <style>
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2%;
        }

        .logo {
            max-height: 150px;
            max-width: 200px;
            float: left;
            padding-right: 10px;
        }
    </style>
@endpush