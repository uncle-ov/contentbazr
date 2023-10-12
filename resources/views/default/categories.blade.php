@extends('layouts.app')

@section('title'){{ trans('misc.categories').' - ' }}@endsection

@section('content')
<section class="section section-sm">
  <div class="container">
    <div class="row">

    <div class="col-lg-12 py-5">
  		<h1 class="mb-0">
  			{{ trans('misc.categories') }}
  		</h1>
  		<p class="lead text-muted mt-0">{{ trans('misc.browse_by_category') }}</p>
  	  </div>

    @include('includes.categories-listing')

    </div><!-- row -->

    <div class="row">
      <div class="col-md-12">
        <h2>Browse Tags</h2>

        <div class="show_more_on_click">
        <?php
          foreach($categories as $category) {
            $sub_cats = json_decode($category->tags_data);
        ?>
        @foreach ($sub_cats as $slug => $distag)
        <a href="{{ URL('tags') . '/' . $slug }}" class="btn btn-sm bg-white border e-none btn-category mb-2">
          {{ $tdistag }}
        </a>
        @endforeach
        </div>
      </div>
    </div>
 </div><!-- container wrap-ui -->
</section>
@endsection
