@extends('layouts.organizer')

@section('content')
  <div class="container">

    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    <h1>Organizer Profile</h1>
    <hr>

    <form method="POST" action="{{ route('organizer.profile.update', [$organizer->id]) }}" enctype="multipart/form-data">
      @method('PUT')
      @csrf

      <div class="row">
        <div class="col-md-4 col-lg-4 col-sm-12">
            <label for="profile_picture" id="profile_picture_label" class="mx-auto d-block">
                <img src="{{ asset($organizer->profile_picture_path) }}" alt="profile picture" id="profile_picture_preview" class="img-circle img-responsive">
                <h3  id="profile_picture_edit"> update </h3>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </label>
        </div>

        <div class="col-md-8 col-lg-8 col-sm-12">

            <div class="form-group">
               <h3>
                  @if ($organizer->email_verified_at )
                    <i class="fas fa-check-circle text-success"></i>
                  @endif
                  {{ $organizer->email }}
                </h3>

                @if (!$organizer->email_verified_at)
                  <button type="submit" form="verification-resend" class="btn btn-link p-0 m-0 align-baseline">Resend verification email</button>.
                @endif
            </div>

            <br>
            <h2>Personal Information</h2>
            <hr>

            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" id="firstname" class="form-control" value="{{ old('firstname') ?? $organizer->firstname }}" required>
                @error('firstname')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" name="lastname" id="lastname" class="form-control" value="{{ old('lastname') ?? $organizer->lastname }}" required>
                @error('lastname')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="mobile_number">Mobile Number:</label>
                <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ old('mobile_number') ?? $organizer->mobile_number }}" placeholder="09 *** *** ***" required>
                @error('mobile_number')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
              <label for="address">Address:</label>
              <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" required>{{ old('address') ?? $organizer->address }}</textarea>
              @error('address')
                <span class="text-danger">{{ $message }}</span>
              @enderror
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-md-4 col-lg-4 col-sm-12">
        </div>

        <div class="col-md-8 col-lg-8 col-sm-12">
          <br>
          <h2>Organization Information</h2>
          <hr>

          <div class="form-group">
              <label for="organization">Organization:</label>
              <input type="text" name="organization_name" id="organization_name" class="form-control" value="{{ old('organization_name') ?? $organizer->organization->name }}" required>
              @error('attendee_organization_name')
                <span class="text-danger">{{ $message }}</span>
              @enderror
          </div>

          <div class="form-group">
              <label for="department">Department:</label>
              <input type="text" name="department" id="department" class="form-control" value="{{ old('department') ?? $organizer->organization->department }}"required >
              @error('department')
                <span class="text-danger">{{ $message }}</span>
              @enderror
          </div>

          <br>
          <div class="form-group">
            Organization Logo:
            <label for="logo" id="logo_label" class="mx-auto d-block">
              @if($organizer->organization->logo)
                <img src="{{ asset($organizer->organization->logo_path) }}" alt="logo" id="logo_preview" accept="image/*">
              @endif
            </label>

            <div class="row">
              <div class="col-md-12">
                  <input class="" name="logo" type="file" id="logo">
              </div>
            </div>
          </div>

          <br>

          <div class="form-group">
            Supporting Documents:

            @if($organizer->organization->supporting_documents)
              <ol>
                @foreach ($organizer->organization->supporting_documents_path as $filename => $path)
                  <li id="document-{{ $loop->index }}">
                    <div class="row">
                      <div class="col-md-8">
                        <a href="{{ $path }}" download target="_blank">{{ $filename }}</a>
                      </div>
                      <div class="col-md-2">
                        <button class="btn btn-sm remove-doc" type="button" data-name="{{ $filename }}" data-target="#document-{{ $loop->index }}"><i class="fas fa-trash"></i></button>
                      </div>
                    </div>
                  </li>
                @endforeach
              </ol>
            @else

            @endif

            <div class="row">
                <div class="col-md-12">
                    <input class="" name="supporting_documents[]" type="file" id="supporting_documents" multiple>
                    <br>
                    {!! hasError($errors, 'supporting_documents') !!}
                </div>
            </div>

          </div>


          <br>
          <h2>Security</h2>
          <hr>

          <div class="row">
              <div class="form-group col-md-6">
                  <label for="password">Password:</label>
                  <input type="password" name="password" id="password" class="form-control">
                  @error('password')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>

              <div class="form-group col-md-6">
                  <label for="password_confirmation">Confirm Password:</label>
                  <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                  @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-12">
          @if ($organizer->email_verified_at)
          <div class="float-right">
              <button type="submit" class="btn btn-primary pull-right">Update</button>
          </div>
          @endif
        </div>
      </div>
    </form>
  </div>

  @if (!$organizer->email_verified_at)
    <form id="verification-resend" method="POST" action="{{ route('verification.resend') }}">
        @csrf
    </form>
  @endif
@endsection

@push('styles')
  <style>
      #profile_picture {
        display: none;
      }

      #profile_picture_preview {
        height: 200px;;
        width: 200px;;
        border-radius: 50%;
      }

      #profile_picture_label {
        position: relative;
        text-align: center;
        position: relative;
      }

      #profile_picture_edit {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display:none;
      }

      #profile_picture_label:hover #profile_picture_preview{
        opacity: 0.5;
        cursor: pointer;
      }

      #profile_picture_label:hover #profile_picture_edit{
        display:block;
      }

      #logo_preview {
        height: 100px;
      }

      #logo_label {
        position: relative;
        text-align: center;
        position: relative;
      }

  </style>
@endpush

@push('scripts')
  <script>
      $(function() {

        $('#profile_picture').on('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#profile_picture_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        $('.remove-doc').on('click', function() {
          $(this).attr('disabled', true)

          let target = $(this).data('target')
          let documentName = $(this).data('name');
          let deleteDocumentEndpoint = "{{ route('organizer.profile.supporting_documents.delete', ['DOCUMENT_NAME']) }}".replace('DOCUMENT_NAME', documentName)

          axios.delete(deleteDocumentEndpoint)
          .then(function (response) {
              if(response.data.result == 'success') {
                  window.Swal.fire(
                      'Document has been removed',
                      'success'
                  )

                  $(target).remove();
              }
          })
          .catch(function (error) {
              console.log(error);
          });
        })
      })
  </script>
@endpush
