@extends('layouts.admin')

@section('content')

    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    <h1>Events</h1>

    <table id="table" class="table table-bordered table-hover"  width="100%">
        <thead class="thead-dark">
            <tr>
                <th>Organizer</th>
                <th>Event</th>
                <th>Schedules</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($events as $event)
                <tr id="{{ $event->code }}">
                    <td>{{ $event->organizer->full_name }}</td>
                    <td>
                        <h4>{{ $event->name }}</h4>
                        <hr>
                        <p>Type: {{ $event->type->name }}</p>
                        <p>Category: {{ $event->category->name }}</p>
                    </td>
                    <td>
                        @include('partials.event_schedules')
                    </td>
                    <td>{{ $event->dynamic_status }}</td>
                    <td>
                        <a href="{{ route('admin.events.show', [$event->code]) }}">view</a>
                    </td>
                </tr>
            @empty

            @endforelse
        </tbody>
    </table>
@endsection

@push('scripts')
    <script src="{{ asset('scripts/admin/events/table.js') }}"></script>
@endpush
