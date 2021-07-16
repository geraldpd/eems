@extends('layouts.admin')

@section('content')
  <div class="container">

    <h1>Create Category</h1>

    <form method="POST" action="{{ route('admin.categories.store') }}">
      @csrf

        <div class="form-group">
            <input type="text" name="name" id="name" class="form-control">
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="custom-control custom-checkbox mr-sm-2">
            <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" checked>
            <label class="custom-control-label" for="is_active">Active</label>
        </div>

        <div class="float-right">
            <a href="{{ route('organizer.events.index') }}" class="float-righ btn btn-link">Cancel</a>
            <button type="submit" class="float-righ btn btn-primary">Submit</button>
        </div>

    </form>

  </div>
@endsection
{{--
@push('scripts')
  <script src="{{ asset('plugins/moment.js') }}"></script>
  <script src="{{ asset('scripts/organizer/events/create.js') }}"></script>
@endpush --}}