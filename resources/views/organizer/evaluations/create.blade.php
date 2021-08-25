@extends('layouts.organizer')

@section('content')
    <div class="container">

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('organizer.evaluations.index') }}">Evaluation Sheets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add</li>
        </ol>

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <form action="{{ route('organizer.evaluations.store') }}" onsubmit="submit.disabled=true; return true;" method="POST">
            @csrf
            @if($event?->code)
                <input type="hidden" name="event" value="{{ $event->code }}">
                <h1 class="text-secondary">Evaluation Sheet for <a class="text-decoration-none" href="{{ route('organizer.events.show', [$event->code]) }}">{{ $event->name }}</a> </h1>
            @endif

            <div class="row">
                <div class="form-group col-md-12">
                    <label for="">Evaluation Title</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') ?? $event ? $event->name.' Evaluation Sheet' : ''}}" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-12">
                    <label for="">Description</label>
                    <textarea name="description" class="form-control" cols="30" rows="5">{{ old('description') ?? $event ? $event->name.' descriptions.' : ''}}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="float-right">
                <a href="{{ route('organizer.events.index') }}" class="float-righ btn btn-link">Cancel</a>
                <button type="submit" name="submit" class="float-righ btn btn-primary">Submit</button>
              </div>
        </form>
    </div>
@endsection

@push('modals')
@endpush

@push('styles')
@endpush

@push('scripts')
@endpush