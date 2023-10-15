@extends('layouts.app')

@section('title')
  {{ trans('auth.sign_up') . ' - ' }}
@endsection

@section('content')
  <a href="{{ url('/') }}"><img src="{{ url('/public/img/logo-main-white.png') }}" alt="logo" width="300"
      class="auth_logo_float"></a>
  <div class="container-fluid">
    <div class="row">

      <div class="col-sm-6 px-0 d-none d-sm-block bg-auth"
        style="background-color: #0f1020;background-image: url({{ URL('public/img/collage.png') }});background-position: 50px center;background-repeat: no-repeat;">
      </div>

      <div class="col-sm-6 login-section-wrapper">
        <a href="{{ url('/') }}" class="mb-4 mb-lg-0 logo-login">
          <img src="{{ url('public/img', $settings->logo) }}" alt="logo" width="200" class="auth_logo">
        </a>
        <div class="login-wrapper my-auto">

          @include('errors.errors-forms')

          @if (session('status'))
            <div class="alert alert-success text-center mt-3">
              <i class="bi bi-stars me-2"></i> {{ session('status') }}
            </div>
          @endif

          @if ($settings->facebook_login == 'on' || $settings->twitter_login == 'on' || $settings->google_login == 'on')
            <div class="d-flex mb-2">
              @if ($settings->facebook_login == 'on')
                <div class="w-100 d-block position-relative mb-2 me-2">
                  <a href="{{ url('oauth/facebook') }}" class="btn btn-lg btn-facebook w-100">
                    <i class="fab fa-facebook me-1"></i> <span
                      class="d-none d-lg-inline-block">Facebook</span>
                  </a>
                </div>
              @endif

              @if ($settings->twitter_login == 'on')
                <div class="w-100 d-block position-relative mb-2 me-2">
                  <a href="{{ url('oauth/twitter') }}" class="btn btn-lg btn-twitter w-100">
                    <i class="fab fa-twitter me-1"></i> <span
                      class="d-none d-lg-inline-block">Twitter</span>
                  </a>
                </div>
              @endif

              @if ($settings->google_login == 'on')
                <div class="w-100 d-block position-relative mb-2">
                  <a href="{{ url('oauth/google') }}" class="btn btn-lg btn-google w-100">
                    <img src="{{ url('public/img/google.svg') }}" class="me-1" width="18"
                      height="18" /> <span class="d-none d-lg-inline-block">Google</span>
                  </a>
                </div>
              @endif

            </div><!-- d-flex -->

            <small class="btn-block text-center my-3 text-uppercase or">{{ trans('misc.or') }}</small>
          @endif

          <h3 class="login-title">{{ trans('auth.sign_up') }}</h3>

          @include('auth.inc.registration-form')

          @if ($settings->registration_active == 1)
            <p class="login-wrapper-footer-text mt-3">
              {{ trans('auth.already_have_an_account') }} <a href="{{ url('login') }}"
                class="text-reset text-decoration-underline">{{ trans('auth.login') }}</a>
            </p>
          @endif

        </div>
      </div>

    </div>
  </div>
@endsection
