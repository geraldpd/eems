@extends('layouts.organizer')

@section('content')
  <div class="container">

    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}">Events</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>

    <form method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label for="name">Name of this event</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Give this event a name!" autofocus>

        @if ($errors->has('name'))
          <small class="help-block text-danger">
            <strong>{{ $errors->first('name') }}</strong>
          </small>
        @endif
      </div>

      <div class="form-group">
        <label>Schedules</label>

        <table class="table table-hover">
          <tbody class="">
            @php
                $today = date('Y-m-d');
            @endphp
            @foreach ($period as $schedule)
              <tr class="schedule-row">
                <td>
                  {{ $schedule->isoFormat('Y MMM D dddd') }}
                </td>
                <td>
                  @php
                    $date_index = $schedule->format('Y-m-d');
                    $min = [
                      'start' => $today == $date_index ? date('H:i') : '',
                      'end' => $min_sched['start']
                    ];
                  @endphp
                    <div class="row schedule-picker" data-day="{{ $date_index }}">
                      <div class="col-md-6 form-group">
                        <label>Start of Event</label>
                        <input
                          type="time"
                          min="{{ $min['start'] }}"
                          id="schedule-{{ $date_index }}-start"
                          name="schedules[{{ $date_index }}][start]"
                          value="{{ old("schedules.$date_index.start") }}"
                          class="form-control schedule_input"
                          required>
                      </div>

                      <div class="col-md-6 form-group">
                        <label>End of Event</label>
                        <input
                          type="time"
                          min="{{ $min['start'] }}"
                          id="schedule-{{ $date_index }}-end"
                          name="schedules[{{ $date_index }}][end]"
                          value="{{ old("schedules.$date_index.end") }}"
                          class="form-control schedule_input"
                          required>
                      </div>
                    </div>
                </td>
              </tr>
            @endforeach
          </tbod>
        </table>

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
          <label for="type_id">Type</label>
          <select name="type_id" id="type_id" class="form-control">
            <option value=""> Select Event Type </option>
              @foreach ($types as $type)
                <option {{ old('type_id') == $type->id ? 'selected' : '' }} value="{{ $type->id }}"> {{ $type->name }} </option>
              @endforeach
          </select>

          @if ($errors->has('type_id'))
            <small class="help-block text-danger">
              <strong>{{ $errors->first('type_id') }}</strong>
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

      <div class="row">
        <div class="col-md-10 form-group">
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

        <div class=" col-md-2 form-group">
          <label for="location">Maximum Particpants</label>
          <input type="number" name="max_participants" id="max_participants" class="form-control" value="{{ old('max_participants') }}" min="1" max="999999">

          @if ($errors->has('max_participants'))
            <small class="help-block text-danger">
              <strong>{{ $errors->first('max_participants') }}</strong>
            </small>
          @endif
        </div>
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
        <p>All attached documents will only be available for download for attending users.</p>

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
                <tr title="This document is not yet attached to this event, press the submit button to save it to this events document folder">
                    <td><a href="{{ route('helpers.download-file', ['document' => $path]) }}" target="_blank" class="text-warning"> {{ $name }} </a></td>
                    <td class="text-center"> <button type="button" data-name="{{ $name }}" class="btn btn-sm btn-secondary remove-document">Remove</button> </td>
                </tr>
              @endforeach
            </tbody>
          </thead>
        </table>
      </div>

      <div class="form-group alert alert-secondary">
        <label for="banner" id="banner_label" class="mx-auto d-block">
          <img src="https://placehold.co/770x250?text=Your+Event+Banner+Here" alt="Event Banner" id="banner_preview" class="img-responsive" >
          <h3  id="banner_edit"> Upload Banner </h3>
          <input type="file" name="banner" id="banner" accept="image/*">
        </label>
        <i class="fas fa-info-circle text-secondary" title="for best result, upload images minimum of 770 x 250"></i>
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

    .schedule_input:invalid {
      color:red;
    }

    #banner {
        display: none;
      }

      #banner_preview {
        /* height: 200px; */
        width: 100%;
      }

      #banner_label {
        position: relative;
        text-align: center;
        position: relative;
      }

      #banner_edit {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display:none;
      }

      #banner_label:hover #banner_preview{
        opacity: 0.5;
        cursor: pointer;
      }

      #banner_label:hover #banner_edit{
        display:block;
      }
  </style>
@endpush

@push('scripts')
  <script>
    const config = {
      save_button: 'submit',
      csrf: '{{ csrf_token() }}',
      tempdocs: {
        count: {{ count($documents) }},
        download: "{{ route('helpers.download-file', ['document' => 'document_path']) }}",
        store: "{{ route('organizer.tempdocs.store') }}",
        destroy: "{{ route('organizer.tempdocs.destroy') }}"
      },
    }
  </script>

  <script src="{{ asset('scripts/organizer/events/helper.uploads.js') }}"></script>
  <script src="{{ asset('scripts/organizer/events/create.js') }}"></script>
@endpush