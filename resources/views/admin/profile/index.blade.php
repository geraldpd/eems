@extends('layouts.admin')

@section('content')
  <div class="container">

    @if(session()->has('message'))
        <div class="alert alert-info">
            {{ session()->get('message') }}
        </div>
    @endif

    <h1>Admin Profile</h1>
    <hr>
    <form method="POST" action="{{ route('organizer.profile.update', [Auth::user()->id]) }}" enctype="multipart/form-data">
      @method('PUT')
      @csrf

      <div class="row">
        <div class="col-md-4 col-lg-4 col-sm-12">
            <label for="profile_picture" id="profile_picture_label">
                <img src="{{ asset(Auth::user()->profile_picture_path) }}" alt="profile picture" id="profile_picture_preview" class="img-circle img-responsive">
                <h3  id="profile_picture_edit"> edit </h3>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </label>
        </div>

        <div class="col-md-8 col-lg-8 col-sm-12">

            <div class="form-group">
               <h3>
                  {{ Auth::user()->email }}
                </h3>
            </div>

            <h2>Personal Information</h2>
            <hr>

            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" id="firstname" class="form-control" value="{{ old('firstname') ?? Auth::user()->firstname }}" required>
                @error('firstname')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" name="lastname" id="lastname" class="form-control" value="{{ old('lastname') ?? Auth::user()->lastname }}" required>
                @error('lastname')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="mobile_number">Mobile Number:</label>
                <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ old('mobile_number') ?? Auth::user()->mobile_number }}" placeholder="09 *** *** ***" required>
                @error('mobile_number')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
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

            <br>

            @if (Auth::user()->email_verified_at)
              <div class="float-right">
                  <button type="submit" class="btn btn-primary pull-right">Update</button>
              </div>
            @endif

        </div>

      </div>

    </form>

  </div>
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
      })
  </script>
@endpush