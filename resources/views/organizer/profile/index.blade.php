@extends('layouts.organizer')

@section('content')
  <div class="container">

    <form method="POST" action="{{ route('organizer.profile.update', [Auth::user()->id]) }}" enctype="multipart/form-data">
      @method('PUT')
      @csrf

      <div class="row">
        <div class="col-md-4 col-lg-4 col-sm-12">

            <label for="profile_picture">
                <img src="{{ Auth::user()->profile_picture ?? asset('assets/default-profile_picture.png') }}" alt="profile picture" id="profile_picture_preview" class="img-circle img-responsive">
                <i class="glyphicon glyphicon-pencil"></i>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </label>
        </div>

        <div class="col-md-8 col-lg-8 col-sm-12">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" name="firstname" id="firstname" class="form-control" value="{{ old('firstname') ?? Auth::user()->firstname }}">
            </div>

            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" name="lastname" id="lastname" class="form-control" value="{{ old('lastname') ?? Auth::user()->lastname }}">
            </div>

            <div class="form-group">
                <label for="mobile_number">Mobile Number</label>
                <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ old('mobile_number') ?? Auth::user()->mobile_number }}">
            </div>

            <div class="float-right">
                <button type="submit" class="btn btn-primary pull-right">Update</button>
            </div>

        </div>

      </div>

    </form>

  </div>
@endsection

@push('styles')
  <style>
      #profile_picture_preview {
        height: 200px;;
        width: 200px;;
        border-radius: 50%;
      }

      #profile_picture {
        display: none;
      }

      #profile-pen {
        z-index: 99;
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