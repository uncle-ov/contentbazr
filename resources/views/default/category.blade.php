@extends('layouts.app')

@section('title'){{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name.' - ' }}@endsection

@section('content')
<section class="section section-sm">
<?php
    $sub_cats = json_decode($category->tags_data);
?>
<div class="container">

  <div class="col-lg-12 py-5">
    <h1 class="mb-0">
      {{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name }}
    </h1>
    <p class="lead text-muted mt-0">
      {{ '('.number_format($images->total()).') '.trans_choice('misc.images_available_category',$images->total()) }}
    </p>
    </div>


<div class="col-lg-12 py-2" style="text-align: right;">
  <select class="form-select" style="display:inline-block;width:auto;" onChange="window.location.href=this.value">
      <option value="<?php echo URL('category/'.$category->slug); ?>">View all</option>
      <?php foreach($sub_cats as $slug => $distag) { ?>
        <?php if(!empty($distag)) { ?>
        <option value="<?php echo URL('tags') . '/' . $slug; ?>">
            <?php echo ucfirst($distag); ?>
        </option>
        <?php } ?>
      <?php } ?>
  </select>
</div>
<!-- Col MD -->
<div class="col-md-12">

  <div class="row">

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

  </div><!-- row -->
 </div><!-- container wrap-ui -->
</section>
@endsection

@section('javascript')

<script type="text/javascript">
 $('#imagesFlex').flexImages({ rowHeight: 320 });
</script>
@endsection
