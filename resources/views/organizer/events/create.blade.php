@extends('layouts.organizer')

@section('content')
  <div class="container">

    @if ($date)
      <h1>{{ $date->format('M d, Y') }}</h1>
    @endif

    <form method="POST" action="{{ route('organizer.events.store') }}">
      @csrf

      <input type="hidden" name="date" value="{{ $date }}">

      <div class="form-group">
        <label for="name">Name of this event</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Give this event a name!" autofocus>

        @if ($errors->has('text'))
        <small class="help-block text-danger">
          <strong>{{ $errors->first('name') }}</strong>
        </small>
        @endif
      </div>

      <div class="row">
        <div class="col-md-6 form-group">
          <label for="name">Start of Event</label>
          <input type="time" id="schedule_start" name="schedule_start" value="{{ old('schedule_start') ?? $min_sched['start'] }}" class="form-control" required>

          @if ($errors->has('schedule_start'))
          <small class="help-block text-danger">
            <strong>{{ $errors->first('schedule_start') }}</strong>
          </small>
          @endif
        </div>

        <div class="col-md-6 form-group">
          <label for="name">End of Event</label>
          <input type="time" id="schedule_end" name="schedule_end" value="{{ old('schedule_end') ?? $min_sched['end'] }}" class="form-control" required>

          @if ($errors->has('schedule_end'))
          <small class="help-block text-danger">
            <strong>{{ $errors->first('schedule_end') }}</strong>
          </small>
          @endif
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 form-group">
          <label for="category_id">Category</label>
          <select name="category_id" id="category_id" class="form-control">
            <option value=""> Select Category </option>
              @foreach ($categories as $category)
              <option {{ old('category_id') == $category->id ? 'selected' : '' }} value="{{ $category->id }}"> {{ $category->name }} </option>
              @endforeach
          </select>

          @if ($errors->has('category_id'))
          <small class="help-block text-danger">
            <strong>{{ $errors->first('category_id') }}</strong>
          </small>
          @endif
        </div>

        <div class="col-md-6 form-group">
          <label for="type">Type</label>
          <select name="type" id="type" class="form-control">
            <option value=""> Select Event Type </option>
              @foreach (config('eems.event_types') as $type)
              <option {{ old('type') == $type ? 'selected' : '' }} value="{{ $type }}"> {{ $type }} </option>
              @endforeach
          </select>

          @if ($errors->has('type'))
          <small class="help-block text-danger">
            <strong>{{ $errors->first('type') }}</strong>
          </small>
          @endif
        </div>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea class="form-control" name="description" id="description" cols="30" rows="10">
          {{ old('description') }}
        </textarea>

        @if ($errors->has('description'))
        <small class="help-block text-danger">
          <strong>{{ $errors->first('description') }}</strong>
        </small>
        @endif
      </div>

      <div class="form-group">
        <label for="location">Location</label>
        <select name="location" id="location" class="form-control">
          <option value=""> Select Location </option>
          <option {{ old('location') == 'venue' ? 'selected' : '' }} value="venue"> Venue </option>
          <option {{ old('location') == 'online' ? 'selected' : '' }} value="online"> Online </option>
        </select>

        @if ($errors->has('location'))
        <small class="help-block text-danger">
          <strong>{{ $errors->first('location') }}</strong>
        </small>
        @endif
      </div>

      <div class="form-group">
        <label for="documents">Documents</label>
        <input type="text" {{ old('documents') }}class="form-control" placeholder="documents">

        @if ($errors->has('documents'))
        <small class="help-block text-danger">
          <strong>{{ $errors->first('documents') }}</strong>
        </small>
        @endif
      </div>

      <div style="form-group">
        <a href="{{ route('organizer.events.index') }}" class="float-righ btn btn-secondary">Cancel</a>
        <button type="submit" class="float-righ btn btn-primary">Submit</button>
      </div>

    </form>

  </div>
@endsection

@push('scripts')
  <script src="{{ asset('scripts/organizer/events/create.js') }}"></script>
@endpush