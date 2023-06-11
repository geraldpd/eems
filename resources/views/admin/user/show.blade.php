@extends('layouts.admin')

@section('content')
  <div class="container">

    <ol class="breadcrumb">
        @php
            $route = $user->hasRole('organizer') ? 'admin.users.organizers' : 'admin.users.attendees';
        @endphp
        <li class="breadcrumb-item"><a href="{{ route($route) }}">User Management</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $user->fullname }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Profile</a></li>
      </ol>


    <h1>Organizer Profile</h1>
    <hr>

      <div class="row">
        <div class="col-md-4 col-lg-4 col-sm-12">
            <label for="profile_picture" id="profile_picture_label" class="mx-auto d-block">
                <img src="{{ asset($user->profile_picture_path) }}" alt="profile picture" id="profile_picture_preview" class="img-circle img-responsive">
                <h3  id="profile_picture_edit"> update </h3>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" readonly>
            </label>
        </div>

        <div class="col-md-8 col-lg-8 col-sm-12">

            <div class="form-group">
               <h3>
                  @if ($user->email_verified_at )
                    <i class="fas fa-check-circle text-success"></i>
                  @endif
                  {{ $user->email }}
                </h3>

                @if (!$user->email_verified_at)
                  <span class="badge badge-warning">User is not yet Verified</span>
                @endif

                @if (!$user->is_approved)
                  <span class="badge badge-warning">User is not yet Approved</span>
                  @else
                  <span class="badge badge-success">User Approved</span>
                @endif
            </div>

            <br>
            <h2>Personal Information</h2>
            <hr>

            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" id="firstname" class="form-control" value="{{ old('firstname') ?? $user->firstname }}" readonly>
                @error('firstname')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" name="lastname" id="lastname" class="form-control" value="{{ old('lastname') ?? $user->lastname }}" readonly>
                @error('lastname')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="mobile_number">Mobile Number:</label>
                <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ old('mobile_number') ?? $user->mobile_number }}" placeholder="09 *** *** ***" readonly>
                @error('mobile_number')
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
          <h2>Other Information</h2>
          <hr>

          @if($user->hasRole('organizer'))
            <div class="form-group">
                <label for="organization">Organization:</label>
                <input type="text" name="organization_name" id="organization_name" class="form-control" value="{{ old('organization_name') ?? $user->organization->name }}" readonly>
                @error('attendee_organization_name')
                  <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="department">Department:</label>
                <input type="text" name="department" id="department" class="form-control" value="{{ old('department') ?? $user->organization->department }}" readonly>
                @error('department')
                  <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" readonly >{{ old('address') ?? $user->address }}</textarea>
                @error('address')
                  <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
              Organization Logo:
              <label for="logo" id="logo_label" class="mx-auto d-block">
                @if($user->organization->logo)
                  <img src="{{ asset($user->organization->logo_path) }}" alt="logo" id="logo_preview" accept="image/*">
                @endif
              </label>
            </div>

            <div class="form-group">
              Supporting Documents:

              @if($user->organization->supporting_documents)
                <ol>
                  @foreach ($user->organization->supporting_documents_path as $filename => $path)
                    <li id="document-{{ $loop->index }}">
                      <div class="row">
                        <div class="col-md-8">
                          <a href="{{ $path }}" download target="_blank">{{ $filename }}</a>
                        </div>
                      </div>
                    </li>
                  @endforeach
                </ol>
              @else

              @endif

            </div>
          @else
            <div class="form-group">
                <label for="lastname">Organization:</label>
                <input type="text" name="attendee_organization_name" id="attendee_organization_name" class="form-control" value="{{ $user->attendee_organization_name }}" readonly>
            </div>

            <div class="form-group">
                <label for="attendee_occupation">Occupation:</label>
                <input type="text" name="attendee_occupation" id="attendee_occupation" class="form-control" value="{{ $user->attendee_occupation }}" readonly>
            </div>
          @endif

        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-12">
          @if (! $user->is_approved)
            <div class="float-right">
              <form action="{{ route("admin.users.approve", [$user->id]) }}" method="POST" id="approve-form">
                @csrf
                <button type="button" class="btn btn-primary pull-right approve-user">Approve</button>
              </form>
            </div>
          @endif
        </div>
      </div>

  </div>

  @if (!$user->email_verified_at)
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

      #logo_preview {
        height: 100px;
      }

  </style>
@endpush

@push('scripts')
  <script>
            $('.approve-user').on('click', function() {
                let user_id = $(this).data('user_id');
                let user_type = $(this).data('user_type');

                window.Swal.fire({
                    title: `Approve Organizer?`,
                    text: 'Are you sure you want to approve this orgnizer?',
                    icon: 'question',
                    confirmButtonText: 'Approve',
                    confirmButtonColor: '#007bff',
                    showCancelButton: true
                })
                .then((result) => {
                    if (!result.isConfirmed) return;
                    let approveForm = $('#approve-form')
                    //approveForm.trigger('submit')
                    approveForm.submit()
                });
            })
  </script>
@endpush
