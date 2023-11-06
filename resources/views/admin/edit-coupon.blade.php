@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <a class="text-reset" href="{{ url('panel/admin/coupons') }}">Coupon</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('misc.add_new') }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

      @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form method="post" action="{{ url('panel/admin/coupons/save') }}" enctype="multipart/form-data">
             @csrf

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Discount Code</label>
		          <div class="col-sm-10">
		            <input placeholder="Enter discount code" name="coupon_code" value="{{ $coupon->code }}" type="text" class="form-control">
		          </div>
		        </div>

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Discount</label>
		          <div class="col-sm-10">
		            <input name="discount" value="{{ $coupon->discount }}" placeholder="Enter discount" type="number" class="form-control">
		          </div>
		        </div>

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Discount Type</label>
		          <div class="col-sm-10">
		            <select class="form-control" name="discount_type">
                  <option {{ $coupon->discount_type == "percentage" ? 'selected' : '' }} value="percentage">Percentage</option>
                  <option {{ $coupon->discount_type == "fixed" ? 'selected' : '' }} value="fixed">Fixed</option>
                </select>
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Start Date</label>
		          <div class="col-sm-10">
		            <input name="start_date" value="{{ $coupon->start_date }}" type="datetime-local" class="form-control">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">End Date</label>
		          <div class="col-sm-10">
		            <input name="end_date" value="{{ $coupon->end_date }}" type="datetime-local" class="form-control">
		          </div>
		        </div>

            <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Description</label>
		          <div class="col-sm-10">
		            <textarea name="description" class="form-control" placeholder="Enter a description">{{ $coupon->description }}</textarea>
		          </div>
		        </div>

						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <input name="coupon_id" type="hidden" value="{{ $coupon->id }}">
		            <button type="submit" class="btn btn-dark mt-3 px-5">{{ __('admin.save') }}</button>
		          </div>
		        </div>

		       </form>

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection

@section('javascript')

<script type="text/javascript"></script>
  @endsection
