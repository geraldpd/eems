@extends('layouts.admin')

@section('content')

    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    @switch(true)
        @case(request()->has('category'))
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Category</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $filter }}</li>
            </ol>
            @break
        @case(request()->has('type'))
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.types.index') }}">Type</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $filter }}</li>
            </ol>
            @break
        @case(request()->has('organizer'))
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.users.organizers') }}">Organizer</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ request()->organizer }}</li>
            </ol>
            @break
    @endswitch

    <h1>Events</h1>

    <table id="table" class="table table-bordered table-hover table-sm"  width="100%">
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
                    <td class="align-middle">{{ $event->organizer->full_name }}</td>
                    <td>
                        <h4>
                            <a href="{{ route('events.show', [$event->code]) }}" target="_blank">
                                {{ $event->name }}
                            </a>
                        </h4>
                        <hr>
                        <p>Type: {{ $event->type->name }}</p>
                        <p>Category: {{ $event->category->name }}</p>
                    </td>
                    <td class="align-middle">
                        @include('partials.event_schedules')
                    </td>
                    <td class="align-middle">{{ $event->dynamic_status }}</td>
                    <td class="align-middle text-center">
                        <a href="{{ route('admin.events.show', [$event->code]) }}">view</a>
                    </td>
                </tr>
            @empty

            @endforelse
        </tbody>
    </table>
@endsection

@push('scripts')
    <script>
        const config = {
            eventsIndex : "{{ route('admin.events.index') }}",
            from: "{{ request()->from }}",
            to: "{{ request()->to }}",
        }
    </script>
    <script src="{{ asset('scripts/admin/events/table.js') }}"></script>
@endpush
