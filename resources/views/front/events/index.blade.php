@extends('layouts.app')

@section('content')

    @forelse ($events as $event)
        {! $event !}
    @empty
        No Event
    @endforelse
@endsection