@extends('layouts.admin')

@section('content')
    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <button type="button" class="btn btn-secondary btn-user active" data-user="attendee">attendees</button>
        <a href="{{ route('admin.users.organizers') }}" class="btn btn-secondary btn-user" data-user="organizer">organizers</a>

        <br>
        <br>

        <table id="table" class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">First</th>
                    <th scope="col">Last</th>
                    <th scope="col">Email</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($attendees as $attendee)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $attendee->firstname }}</td>
                        <td>{{ $attendee->lastname }}</td>
                        <td>{{ $attendee->email }}</td>
                        <td>{!! tableActions($attendee, 'admin.users') !!}</td>
                    </tr>
                @empty

                @endforelse
            </tbody>

        </table>
    </div>
@endsection

@push('scripts')
    {{-- {!! tableScript('categories') !!} --}}
@endpush

