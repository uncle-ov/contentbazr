@extends('layouts.app')
@section('content')

<style>
  .e404 {
    font-size: 18em;
  }

  @media(max-width: 768px) {
    .e404 {
      font-size: 8em;
    }
  }
</style>
<section id="whatwedo" class="section py-5">
  <div class="container">
      <div class="text-center">
          <h1 class="e404">404</h1>
          <p>Sorry, we can't find the page you're looking for.</p>

          <div style="padding-top: 30px;"></div>
          <a href="{{ url('') }}" class="btn btn-lg btn-main rounded-pill btn-outline-custom  px-4 arrow">Back to Homepage</a>  
      </div>
  </div>
</section>

@endsection
@section('javascript')
<script type="text/javascript">
    $('#imagesFlex').flexImages({ rowHeight: 320, maxRows: 8, truncate: true });
    
    @if (session('success_verify'))
    swal({
        title: "{{ trans('misc.welcome') }}",
        text: "{{ trans('users.account_validated') }}",
        type: "success",
        confirmButtonText: "{{ trans('users.ok') }}"
        });
    @endif
    
    @if (session('error_verify'))
    swal({
        title: "{{ trans('misc.error_oops') }}",
        text: "{{ trans('users.code_not_valid') }}",
        type: "error",
        confirmButtonText: "{{ trans('users.ok') }}"
        });
    @endif
    
</script>
@endsection