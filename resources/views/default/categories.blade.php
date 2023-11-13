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

    <?php
      $all_tags = [];

      foreach($categories as $category) {
        $sub_cats = json_decode($category->tags_data);

        foreach ($sub_cats as $slug => $distag) {
          if(!empty($distag) && strlen($distag) > 2) {
            $all_tags[$distag] = URL('tags') . '/' . $slug;
          }
        }
      }

      ksort($all_tags);
      $previous_tag_title = '';
    ?>

    <div class="row">
      <div class="col-md-12">
        <h4 class="mt-5">Browse Tags</h4>
        <p>Browse stock templates by subcategories tags</p>

        <div class="show_more_on_click">
          @foreach($all_tags as $title => $url)
          @if(empty($previous_tag_title) || substr($title, 0, 1) != substr($previous_tag_title, 0, 1))
          <div style="position: relative">
            <hr>
            <span style="text-transform: uppercase;position: absolute;display: table;top: -16px;padding-right: 10px;background: white;font-size: 20px;font-weight: bold;color: #999;">
              {{ substr($title, 0, 1) }}
            </span>
          </div>
          @endif
    
          <a href="{{ $url }}" class="btn btn-sm bg-white border e-none btn-category mb-2">
            {{ $title }}
          </a>
          <?php $previous_tag_title = $title; ?>
          @endforeach
          <a class="show_all">Show All</a>
        </div>
      </div>
    </div>
 </div><!-- container wrap-ui -->
</section>
@endsection
