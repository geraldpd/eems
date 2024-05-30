@extends('layouts.app')

@section('content')
    <section class="section contact-form">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h3>Register as <span class="alternate">{{ request()->as ? ucfirst(request()->as) : 'Attendee' }}</span></h3>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="register">
                @csrf

                @if (request()->event && request()->email)
                    <input type="hidden" name="code" value="{{ request()->event }}">
                    <input type="hidden" name="encrypted_email" value="{{ request()->email }}">
                @endif

                @if (request()->has('as') && request()->as == 'organizer')
                    <input type="hidden" name="as" value="organizer">

                    <div class="step-2">
                        <br>
                        <h4>Organization Information</h4>

                        <div class="form-group row">

                            <label for="organization_type" class="col-md-4 col-form-label text-md-right">{{ __('Organization Type') }}</label>

                            <div class="col-md-6">

                                <select style="height: 50px;" id="organization_type" class="form-control" name="organization_type" value="{{ old('organization_type') }}" required>
                                    @foreach (config('eems.organization_types') as $type => $name)
                                        <option {{ old('organization_type') == $type ? 'selected' : '' }} value="{{ $type }}"> {{ $name }} </option>
                                    @endforeach
                                </select>

                                @error('organization_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="organization_name" class="col-md-4 col-form-label text-md-right">{{ __('Organization') }}</label>

                            <div class="col-md-6">
                                <input id="organization_name" type="text" class="form-control @error('organization_name') is-invalid @enderror" name="organization_name" value="{{ old('organization_name') }}" required autocomplete="organization_name"
                                    autofocus>

                                @error('organization_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="department" class="col-md-4 col-form-label text-md-right">{{ __('Department / College') }}</label>

                            <div class="col-md-6">
                                <input id="department" type="text" class="form-control @error('department') is-invalid @enderror" name="department" value="{{ old('department') }}" required autocomplete="department" required autofocus>

                                @error('department')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>

                            <div class="col-md-6">
                                <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" required>{{ old('address') }}</textarea>

                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="logo" class="col-md-4 col-form-label text-md-right">{{ __('Logo') }}</label>

                            <div class="col-md-6">
                                <input class="" name="logo" type="file" id="logo" accept="image/png, image/jpeg">
                                @error('logo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="supporting_documents" class="col-md-4 col-form-label text-md-right">{{ __('Supporting Documents') }}</label>

                            <div class="col-md-6">
                                <input class="" name="supporting_documents[]" type="file" id="supporting_documents" multiple>
                                <br>
                                {!! hasError($errors, 'supporting_documents') !!}
                            </div>
                        </div>

                    </div>
                @endif

                <div class="step-1">
                    <h4>Representative Information</h4>

                    <div class="form-group row">
                        <label for="firstname" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

                        <div class="col-md-6">
                            <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname') }}" required autocomplete="firstname" autofocus>

                            @error('firstname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="lastname" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>

                        <div class="col-md-6">
                            <input id="lastname" type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname') }}" required autocomplete="lastname" autofocus>

                            @error('lastname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="mobile_number" class="col-md-4 col-form-label text-md-right">{{ __('Mobile Number') }}</label>

                        <div class="col-md-6">
                            <input id="mobile_number" type="text" maxlength="11" class="form-control @error('mobile_number') is-invalid @enderror" name="mobile_number" value="{{ old('mobile_number') }}" required placeholder="09 *** *** ***"
                                autofocus>

                            @error('mobile_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ request()->email ? decrypt(request()->email) : old('email') }}"
                                {{ request()->email ? 'readonly' : '' }} required autocomplete="email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                        <div class="col-md-6">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                </div>

                @if (request()->has('as') && request()->as == 'attendee')
                    <input type="hidden" name="as" value="attendee">
                    <div class="step-2">
                        <br>
                        <h4>Other Information</h4>

                        <div class="form-group row">
                            <label for="firstname" class="col-md-4 col-form-label text-md-right">{{ __('Organization') }}</label>

                            <div class="col-md-6">
                                <input id="attendee_organization_name" type="text" class="form-control @error('organization') is-invalid @enderror" name="attendee_organization_name" value="{{ old('attendee_organization_name') }}" required
                                    autofocus>

                                @error('attendee_organization_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="attendee_occupation" class="col-md-4 col-form-label text-md-right">{{ __('Occupation') }}</label>

                            <div class="col-md-6">
                                <input id="attendee_occupation" type="text" class="form-control @error('attendee_occupation') is-invalid @enderror" name="attendee_occupation" value="{{ old('attendee_occupation') }}" required autofocus>

                                @error('attendee_occupation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>

                            <div class="col-md-6">
                                <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" required>{{ old('address') }}</textarea>

                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                @endif

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">

                        <button type="button" class="btn btn-primary btn-main-md copy-right-modal-trigger">{{ __('Register') }} </button>

                    </div>
                </div>
            </form>

        </div>
    </section>

    <div class="modal fade" id="copy-right-modal" tabindex="-1" aria-labelledby="copy-right-modal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Copy Privacy Notice</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>1. Personal information we collect and keep</h4>
                    <p>We collect, use and keep your personal information, which include but are not limited, to the following:</p>
                        <ul>
                            <li>Personal Information</li>
                            <li>Higher Education Institutions Information</li>
                            <li>Event Details </li>
                        </ul>

                    <h4>2. With whom your data is shared</h4>
                    <p>We do not share your personal information with third parties except in the following circumstances:</p>
                    <ul>
                        <li>With your consent.</li>
                        <li>To service providers and partners who assist us in operating the EMS.</li>
                        <li>To comply with legal obligations, court orders or government requests.</li>
                        <li>To protect the rights, property, or safety of EventHEI, our users, and others.</li>
                    </ul>

                    <h4>3. Data Security</h4>
                    <p>Your personal information is stored in secured databases. We implement appropriate technical and organizational measures to safeguard your personal information against unauthorized access, alteration, disclosure, or destruction.
                    </p>

                    <h4>4. Changes to this Privacy Notice</h4>
                    <p>We may update this Privacy Notice from time to time. We will notify you of any significant changes by posting the new Privacy notice on our EMS and updating the “Last Updated” date.</p>

                    <h4>5. Contact us</h4>
                    <p>You may email <a href="mailto:eventhei@gmail.com">eventhei@gmail.com</a> if you have questions about the Data privacy Act and when you need to request a copy of your personal documents.</p>

                    <p>I acknowledge that I have read and understood the EventHEI Data Privacy Notice. I agree to the processing of my personal data in accordance with the Data Privacy Policy.</p>
                </div>

                <div class="footer text-center mb-2 mt-2">
                    <button type="submit" form="register" class="btn btn-main-md agree-register">
                        Agree and Register
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.copy-right-modal-trigger').on('click', function() {
                    $('#copy-right-modal').modal("show");
                })

                $('.agree-register').on('click', function() {
                    $(this).attr('disabled', true)
                    $('#register').submit();
                })
            })
        </script>
    @endpush
@endsection
