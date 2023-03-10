@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ isset($url) ? ucwords($url) : 'Merchant '}} {{ __('Login') }}</div>

                <div class="card-body">

                    @error('sessionExpired')
                        <div class="alert alert-danger text-center">
                            {{ $message }}
                        </div>
                    @enderror

                    <form method="POST" action="{{ isset($url) ? route("$url.login") : route('login') }}">
                        
                        @csrf

                        <div class="form-group row">
                            <label for="emailOrMobileOrUsername" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail / Username / Mobile') }}</label>

                            <div class="col-md-6">
                                <input id="emailOrMobileOrUsername" type="text" class="form-control @error('emailOrMobileOrUsername') is-invalid @enderror" name="emailOrMobileOrUsername" value="{{ old('emailOrMobileOrUsername') }}" required autocomplete="email" autofocus>

                                @error('emailOrMobileOrUsername')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
