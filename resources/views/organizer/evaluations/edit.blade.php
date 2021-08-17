@extends('layouts.organizer')

@section('content')
    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="container">
        <div class="row questions-div"></div>

        <hr>

        <div class="row jumbotron">
            <div class="col-md-8">
                <select class="form-control" name="evaluation_type" id="evaluation_type">
                    <option value=""> Select Item </option>
                    @foreach (config('eems.evaluation_types') as $evaluation_type => $attributes)
                        <option value="{{ $evaluation_type }}" data-attributes='@json($attributes)'> {{ ucwords($evaluation_type) }} </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <button type="submit" id="add-evaluation_type" class="btn btn-primary mb-2 btn-block">Add Item</button>
            </div>

            <div class="col-md-8 ">
                <div class="row form_builder-div"></div>
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
        const config = {
            evaluation_type: @json(config('eems.evaluation_types'))
        }
    </script>
    <script src="{{ asset('scripts/organizer/evaluations/edit.js') }}"></script>
@endpush