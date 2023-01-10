@extends('layouts.app')

@section('content')

<section class="section contact-form">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-6">
				<div class="section-title">
					<h3>Sign<span class="alternate">Up</span></h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maiores, velit.</p>
				</div>
			</div>
		</div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group row justify-content-center">

                <div class="col-md-6">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="email" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row justify-content-center">

                <div class="col-md-6">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="password" required autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            {{-- <div class="form-group row">
                <div class="col-md-6 offset-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
            </div> --}}

            <div class="form-group row mb-0 justify-content-center">
                <div class="">
                    <button type="submit" class="block btn btn-main-md">
                        {{ __('Login') }}
                    </button>

                    {{-- @if (Route::has('password.request'))

                    This is working, but the template is not yet applied
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif --}}
                </div>
            </div>
        </form>


	</div>
</section>
@endsection
