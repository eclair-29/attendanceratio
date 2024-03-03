@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-4">
            <div class="card" id="login_card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="post" action="{{ route('login') }}">
                        @csrf

                        <div class="pb-3">
                            <label for="username" class="pb-2">{{ __('Username')}}</label>

                            <input id="username" type="text"
                                class="form-control @error('username') is-invalid @enderror" name="username"
                                value="{{ old('username') }}" required autocomplete="username" autofocus>

                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="pb-3">
                            <label for="password" class="pb-2">{{ __('Password')}}</label>

                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password" required
                                autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{
                                old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>

                        <button type="submit" class="btn btn-outline-success px-5">
                            {{ __('Login') }}
                        </button>

                        @if (Route::has('password.request'))
                        <!-- <a class="d-block btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a> -->
                        @endif
                    </form>
                </div>

                <div class="card-footer text-body-secondary text-center">
                    HR Attendance Ratio Monitoring v.1.0.0
                </div>
            </div>
        </div>
        <div class="col">
            <div id="bg" style=""></div>
            <div id="headline" class="text-center d-flex align-items-center justify-content-center"
                style="height: calc(100vh - 100px)">
                HR Attendance Ratio Monitoring
            </div>
        </div>
    </div>
</div>
@endsection