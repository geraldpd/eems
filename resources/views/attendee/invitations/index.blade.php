@extends('layouts.auth.attendee')

@section('content')
    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <h1 class="text-secondary">MY INVITATIONS</h1>
        <div class="row justify-content-center">

            @forelse ($events as $event)

                {{ $event->name }}
            @empty

            <p>No one invited you</p>
            @endforelse

        </div>
    </div>
@endsection
