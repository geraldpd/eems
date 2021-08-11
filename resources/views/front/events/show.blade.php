@extends('layouts.app')

@section('content')

    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="container">

        <h1 class="display-5">{{ $event->name }}</h1>

        <h4>{{ $event->schedule_start->format('h:ia') }} - {{ $event->schedule_end->format('h:ia') }} of {{ $event->schedule_start->format('M d, Y') }}</h4>

        <br>

        {{ $event->description }}

    </div>
@endsection