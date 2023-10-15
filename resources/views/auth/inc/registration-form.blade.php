<form
  action="{{ isset($admin_user_creation) ? url('panel/admin/members/create') : url('register') }}"
  method="post"
  id="signup_form"
>

  <input type="hidden" name="_token" value="{{ csrf_token() }}">

  @if ($settings->captcha == 'on')
    @captcha
  @endif

  <div class="row">

    <div class="col-md-6">
      <div class="form-floating mb-3">
        <input type="text" required class="form-control" id="inputname" value="{{ old('username') }}"
          name="username" placeholder="{{ trans('auth.username') }}" autocomplete="off">
        <label for="inputname">{{ trans('auth.username') }}</label>
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-floating mb-3">
        <input type="email" required class="form-control" id="inputemail" value="{{ old('email') }}"
          name="email" placeholder="{{ trans('auth.email') }}" autocomplete="off">
        <label for="inputemail">{{ trans('auth.email') }}</label>
      </div>
    </div>

  </div><!-- row -->

  <div class="form-floating mb-3">
    <input type="password" minlength="8" required class="form-control showHideInput" id="inputepassword"
      name="password" placeholder="{{ trans('auth.password') }}">
    <label for="inputpassword">{{ trans('auth.password') }}</label>

    <span class="input-show-password" id="showHidePassword">
      <i class="far fa-eye-slash"></i>
    </span>
  </div>

  <div class="form-floating mb-3">
    <input type="password" minlength="8" required class="form-control showHideInput" id="inputepassword2"
      name="password_confirmation" placeholder="{{ trans('auth.confirm_password') }}">
    <label for="inputepassword2">{{ trans('auth.confirm_password') }}</label>
  </div>

  @if(isset($admin_user_creation))
  <input type="hidden" value="1" name="admin_user_creation">
  @else
  <div class="form-check mb-3">
    <input class="form-check-input" required type="checkbox" name="agree_gdpr" value="1" id="flexCheckDefault"
      @if (old('agree_gdpr')) checked="checked" @endif>
    <label class="form-check-label" for="flexCheckDefault">
      {{ trans('admin.i_agree_gdpr') }}

      @if ($settings->link_privacy != '')
        <a href="{{ $settings->link_privacy }}" target="_blank">{{ trans('admin.privacy_policy') }}</a>
      @endif

    </label>
  </div>
  @endif

  <button type="submit" id="buttonSubmitRegister"
    class="btn w-100 btn-lg btn-custom">{{ trans('auth.sign_up') }}</button>

  @if ($settings->captcha == 'on')
    <small class="d-block mt-3">
      {{ trans('misc.protected_recaptcha') }}
      <a href="https://policies.google.com/privacy" target="_blank">{{ trans('misc.privacy') }}</a> - <a
        href="https://policies.google.com/terms" target="_blank">{{ trans('misc.terms') }}</a>
    </small>
  @endif

</form>
