@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">Coupons ({{$data->count()}})</span>

			<a href="{{ url('panel/admin/coupons/add') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
				<i class="bi-plus-lg"></i> {{ trans('misc.add_new') }}
			</a>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check2 me-1"></i>	{{ session('success_message') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                  <i class="bi bi-x-lg"></i>
                </button>
                </div>
              @endif

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

					<div class="table-responsive p-0">
						<table class="table table-hover">
						 <tbody>

               @if ($data->count() !=  0)
                  <tr>
                     <th class="active">ID</th>
                     <th class="active">Coupon Code</th>
                     <th class="active">Discount</th>
                     <th class="active">Type</th>
                     <th class="active">Status</th>
                     <th class="active">Start Date</th>
                     <th class="active">End Date</th>
                     <th class="active">Action</th>
                   </tr>

                 @foreach ($data as $coupon)
                   <tr>
                     <td>{{ $coupon->id }}</td>
                     <td>{{ $coupon->code }}</td>
                     <td>{{ $coupon->discount }}</td>
                     <td>{{ $coupon->discount_type }}</td>
                     <td><span class="badge bg-{{ isCouponValid($coupon->code) ? 'success' : 'warning' }}">{{ isCouponValid($coupon->code) ? 'Valid' : 'Invalid' }}</span></td>
                     <td>{{ $coupon->start_date }}</td>
                     <td>{{ $coupon->end_date }}</td>
                     <td>
                       <a href="{{ url('panel/admin/coupons/edit/').'/'.$coupon->id }}" class="text-reset fs-5 me-2">
                         <i class="far fa-edit"></i>
                       </a>

                      <form method="POST" action="{{ url('panel/admin/coupons/delete', $coupon->id) }}" accept-charset="UTF-8" class="d-inline-block align-top" onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                        @csrf
                        <button class="btn btn-link text-danger e-none fs-5 p-0 actionDelete" type="submit"><i class="bi-trash-fill"></i></button>
                      </form>

										</td>
                   </tr><!-- /.TR -->
                   @endforeach

									@else
										<h5 class="text-center p-5 text-muted fw-light m-0">{{ trans('misc.no_results_found') }}</h5>
									@endif

								</tbody>
								</table>
							</div><!-- /.box-body -->

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
