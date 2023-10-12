@extends('layouts.app')

@section('title'){{ $title.' - ' }}@endsection

@section('content')
<?php $sub_cats = json_decode($category->tags_data); ?>

<section class="section section-sm">

<div class="container">
<div class="row">
  <div class="col-lg-12 py-5">
    <h1 class="mb-0 text-break">
      {{ $tags }}
    </h1>

    <p class="lead text-muted mt-0">
      {{trans('misc.tagged_images' )}} ({{$total}})
    </p>

    </div>
<?php if(!empty($category)) { ?>
<div class="col-lg-12 py-2" style="text-align: right;">
  <a href="{{ URL('category/'.$category->slug) }}" class="btn btn-main btn-secondary" style="float: left;">
    Go Back
  </a>
  <select class="form-select" style="display:inline-block;width:auto;" onChange="window.location.href=this.value">
      <optgroup label="<?php echo $category->name; ?>">
      <option value="<?php echo URL('category/'.$cat_slug); ?>">View all</option>
      <?php foreach($sub_cats as $dis_slug => $distag) { ?>
        <option <?php if($dis_slug == $slug) echo ' selected'; ?> value="<?php echo URL('tags') . '/' . $dis_slug; ?>">
            <?php echo $distag; ?>
        </option>
      <?php } ?>
      </optgroup>
  </select>
</div>
<?php } ?>

<!-- Col MD -->
<div class="col-md-12">

	@if ($images->total() != 0)

    <div class="dataResult">
       @include('includes.images')
       @include('includes.pagination-links')
     </div>

	  @else
	    		<h3 class="mt-0 fw-light">
	    		{{ trans('misc.no_results_found') }}
	    	</h3>
	  @endif

 </div><!-- /COL MD -->
</div><!-- row -->
 </div><!-- container wrap-ui -->
</section>
@endsection

@section('javascript')

<script type="text/javascript">
 $('#imagesFlex').flexImages({ rowHeight: 320 });
 </script>
@endsection
