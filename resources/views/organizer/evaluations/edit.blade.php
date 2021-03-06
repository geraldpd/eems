@extends('layouts.organizer')

@section('content')
    <div class="container">

        <ol class="breadcrumb">
            @if($event)
                <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
                <li class="breadcrumb-item"><a href="{{ route('organizer.events.evaluations.index', [$event->code]) }}">{{ $event->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ ucwords($event->evaluation->name) }}</li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            @else
                <li class="breadcrumb-item"><a href="{{ route('organizer.evaluations.index') }}">Evaluation Sheets</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ ucwords($evaluation->name) }}</li>
            @endif
        </ol>

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        @if($event?->code)
            <input type="hidden" name="event" value="{{ $event->code }}">
            <h1 class="text-secondary">Evaluation Sheet for <a class="text-decoration-none" href="{{ route('organizer.events.show', [$event->code]) }}">{{ $event->name }}</a> </h1>
        @endif

        <div class="row">
            <div class="form-group col-md-12">
                <label for="">Evaluation Title</label>
                <input type="text" id="preview-name" class="form-control" value="{{ old('name') ?? $evaluation->name }}" required>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-12">
                <label for="">Description</label>
                <textarea name="preview-description" id="preview-description"class="form-control" cols="30" rows="5">{{ old('description') ?? $evaluation->description }}</textarea>
                @error('description')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="alert alert-secondary">
            <ol class="questions-div">
                @error('description')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                {!! $evaluation->html_form ? $evaluation->html_form : '<h2 class="empty-form_text text-muted">No Evaluation Entries </h2>' !!}
            </ol>
        </div>

        <div class="alert alert-dark">
            <div class="row">
                <div class="col-md-8">
                    <select class="form-control" name="evaluation_type" id="evaluation_type">
                        <option value=""> Select Form Type </option>
                        @foreach (config('eems.evaluation_types') as $evaluation_type => $attributes)
                            <option value="{{ $evaluation_type }}" data-attributes='@json($attributes)'> {{ ucwords($evaluation_type) }} </option>
                        @endforeach
                    </select>

                    <div class="row form_builder-div mt-2"></div>
                </div>

                <div class="col-md-4">
                    <div class="form-creation-buttons">
                        <button type="button" id="add-evaluation_type" class="btn btn-light mb-2 btn-block">Add Entry</button>
                        <button type="button" id="clear-evaluation_type" class="btn btn-secondary mb-2 btn-block">Clear Form</button>
                        <br>

                        <form id="evaluation-form" action="{{ route('organizer.evaluations.update', [$evaluation->id]) }}" onsubmit="save_evaluation_form.disabled=true; return true;" method="POST">
                            @csrf
                            @method('PUT')
                            @if($event?->code)
                                <input type="hidden" name="event" value="{{ $event->code }}">
                            @endif
                            <input type="hidden" name="name" id="name" value="{{ old('name') }}">
                            <input type="hidden" name="description" id="description" value="{{ old('description') }}" required>
                            <input type="hidden" name="html_form" id="html_form" value="{{ old('html_form') }}" required>
                            <input type="hidden" name="questions" id="questions" value="{{ old('questions') }}" required>
                            <button type="button" id="save-evaluation_form" name="save_evaluation_form" class="btn btn-primary mb-2 btn-block">Save Evaluation Sheet</button>
                        </form>
                    </div>

                    <div class="form-modification-buttons d-none">
                        <button type="button" id="update-evaluation_type" class="btn btn-light mb-2 btn-block">Update Entry</button>
                        <button type="button" id="cancel-evaluation_type" class="btn btn-secondary mb-2 btn-block">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
@endpush

@push('styles')
@endpush

@push('scripts')
    <script type="text/javascript">
        const sortable = new window.draggable.Sortable($('.questions-div').get(0), {
            draggable: 'li',
            delay: 1000,
        });

        const config = {
            evaluation: @json($evaluation),
            evaluation_type: @json(config('eems.evaluation_types')),
            event: @json($event),
            events_count: {{ $evaluation->events_count }}
        }

        if(parseInt({{ session()->has('clear_storage') ? 1: 0 }})) {
            localStorage.removeItem('html_form');
        }

        if(localStorage.getItem('html_form')) {
            $('.questions-div').html(localStorage.getItem('html_form'));
        }

        console.log(config)
    </script>
    <script src="{{ asset('scripts/organizer/evaluations/edit.js') }}" defer></script>
@endpush