@extends('layouts.organizer')

@section('content')
  <div class="container">

    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>

    @if ($date)
      <h1>{{ $date->format('M d, Y') }}</h1>
    @endif

    <form method="POST" action="{{ route('organizer.events.store') }}">
      @csrf

      <input type="hidden" name="date" value="{{ $date }}">

      <div class="form-group">
        <label for="name">Name of this event</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Give this event a name!" autofocus>

        @if ($errors->has('name'))
          <small class="help-block text-danger">
            <strong>{{ $errors->first('name') }}</strong>
          </small>
        @endif
      </div>

      <div class="row">
        <div class="col-md-6 form-group">
          <label for="name">Start of Event</label>
          <input type="time" max="23:29" id="schedule_start" name="schedule_start" value="{{ old('schedule_start') ?? $min_sched['start'] }}" class="form-control" required>

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
        <textarea class="form-control" name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea>

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

      <div class="location-additional-field">

        <div class="form-group location-venue d-none">
          <label for="location-venue">Venue</label>
          <textarea class="form-control" name="venue" id="location-venue" cols="30" rows="2" placeholder="Complete address to the venue">{{ old('venue') }}</textarea>

          @if ($errors->has('venue'))
            <small class="help-block text-danger">
              <strong>{{ $errors->first('venue') }}</strong>
            </small>
          @endif
        </div>

        <div class="form-group location-online d-none">
          <label for="location-online">Link</label>
          <input type="url" name="online" id="location-online" value="{{ old('online') }}" class="form-control" placeholder="link to the online event" autofocus>

            @if ($errors->has('online'))
              <small class="help-block text-danger">
                <strong>{{ $errors->first('online') }}</strong>
              </small>
            @endif
        </div>

      </div>

      <div class="form-group alert alert-secondary">
        <label>Documents</label>

        <div class="documents"></div>

        <div class="documents-progressbar"></div>

        <table class="uploaded-documents table-sm table table-condensed table-hover">
          <thead>
            <tr>
                <th>Document</th>
                <th class="text-center">Action</th>
            </tr>
            <tbody>
              @foreach ($documents as $name => $path)
                <tr>
                    <td><a href="{{ $path['asset'] }}" target="_blank"> {{ $name }} </a></td>
                    <td class="text-center"> <button type="button" data-name="{{ $name }}" class="btn btn-sm btn-secondary remove-document">Remove</button> </td>
                </tr>
              @endforeach
            </tbody>
          </thead>
        </table>

      </div>

      <div class="float-right">
        <a href="{{ route('organizer.events.index') }}" class="float-righ btn btn-link">Cancel</a>
        <button type="submit" class="float-righ btn btn-primary">Submit</button>
      </div>

    </form>

  </div>
@endsection

@push('styles')
  <style>
    .ck-editor__editable_inline {
        min-height: 400px;
    }
  </style>
@endpush

@push('scripts')
  <script>
    const config = {
      tempdocs: {
        count: {{ count($documents) }},
        store: "{{ route('organizer.tempdocs.store') }}",
        destroy: "{{ route('organizer.tempdocs.destroy') }}"
      },
      csrf: '{{ csrf_token() }}'
    }
  </script>

  <script src="{{ asset('scripts/organizer/events/create.js') }}"></script>
@endpush