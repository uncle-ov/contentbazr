<?php
	$default_license = ['Free for commercial use','Free for personal use','Digital or print media allowed','Unlimited use allowed','Can be used globally','Modifications allowed','Derivative works allowed',];
	
	$default_use_case = ['Primarily as Instagram Story','Mobile Presentation','Facebook Story','Pinterest Story','Twitter Cards','Linked in Story','Virtual Invitation Card',];
	
	$license = !empty($response->license) ? explode(PHP_EOL, $response->license) : $default_license;
	
	$usecase = !empty($response->use_case) ? explode(PHP_EOL, $response->use_case) : $default_use_case;

    function renderThumbnail($url, $settings) {
    ?>
        @if (empty($url['video']))
        @php
            $previewWidth = $url['resolution']->width;
            $previewHeight = $url['resolution']->height;
            
            $css_w = $previewWidth > $previewHeight ? '100%' : '400px';
        @endphp
            <div style="margin: 0 auto; background: url('{{asset('public/img/pixel.gif')}}') repeat center center; max-width:{{$css_w}};">
                <a href="{{ $url['url'] }}" class="glightbox" style="cursor: zoom-in;">
                <img class="img-fluid lazyload" style="display: inline-block; width: {{$css_w}}" src="{{ $url['url'] }}" data-src="{{ $url['url'] }}" />
                </a>
            </div>
        @else
        <a href="{{ $url['url'] }}" class="glightbox" style="cursor: zoom-in;">
            <video width="100%" controls  class="glightbox">
              <source src="{{$url['url']}}" type="{{ $url['mime_type'] }}">
              Your browser does not support HTML video.
            </video>
        </a>
        @endif
    <?php }

    $thumbs = json_decode($response->thumbnail);
    $is_json = (!empty($thumbs) && !empty($thumbs[0]));

    $thumburl = Helper::getThumbUrl($thumbs[0]);
    
    $thumbnails = [];
    if(is_array($thumbs)) {
        foreach($thumbs as $thumb) {
            $thumbnails[] = [
                'url' => Helper::getThumbUrl($thumb),
                'resolution' => $thumb->resolution,
            ];
        }
        
        $json_videos = (json_decode($response->video)) ? json_decode($response->video) : [];
    
        foreach($json_videos as $vidoe) {
            $thumbnails[] = [
                'url' => Storage::disk('azure')->url('/uploads/'.$vidoe->file),
                'mime_type' => $vidoe->mime_type,
                'video' => true,
            ];
        }
    } else {
        $thumbnails = [Storage::disk('azure')->url($thumbs)];
    }
    
    $stock_resolution = '';
    $stock_name = '';
?>
@extends('layouts.app')
<style>
  .coupon_applied {
    background: #ffeed8;
    padding: 7px 15px;
    border-radius: 5px;
    border: 1px dashed rgba(0,0,0,.1);
    font-weight: bold;
    text-transform: uppercase;
    font-size: 13px;
    line-height: 24px;
    text-align: center;
  }
</style>
@section('title'){{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id.' - ' }}@endsection

@section('description_custom'){{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id.' - ' }} @if ($response->description != ''){{ Helper::removeLineBreak(e($response->description)).' - ' }}@endif @endsection

@section('keywords_custom'){{$response->tags .','}}@endsection

@section('css')
<meta property="og:type" content="website" />

<meta property="og:site_name" content="{{$settings->title}}"/>
<meta property="og:url" content="{{url("template/$response->id").'/'.str_slug($response->title)}}"/>
<meta property="og:image" content="{{$thumburl}}"/>
<meta property="og:title" content="{{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id }}"/>
<meta property="og:description" content="{{ Helper::removeLineBreak( e( $response->description ) ) }}"/>

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="{{$thumburl}}" />
<meta name="twitter:title" content="{{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id }}" />
<meta name="twitter:description" content="{{ Helper::removeLineBreak( e( $response->description ) ) }}"/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

@endsection

@section('content')

@section('content')

@if ($response->item_for_sale == 'free' && $response->user()->id != auth()->id())
  <!-- start thanks to author sharing -->
  <div class="fixed-bottom display-none" id="alertThanks">
    <div class="d-flex justify-content-center align-items-center">
      <div class="alert-thanks shadow-sm mb-3 mx-2 position-relative alert-dismissible">
        <button type="button" class="btn-close text-dark" id="closeThanks">
          <i class="bi bi-x-lg"></i>
        </button>

        <div class="d-flex">
          <div class="flex-shrink-0">
            <img class="img-fluid rounded img-thanks-share" width="100" src="{{ Helper::getThumbUrl($response->thumbnail) }}" />
          </div>
          <div class="flex-grow-1 ms-3">
            <h5>{{ __('misc.give_thanks') }} <i class="bi-stars text-warning"></i></h5>
            {!! __('misc.thanks_to_author_sharing', ['username' => '<strong>'.$response->user()->username.'</strong>']) !!}

            <ul class="list-inline mt-2 fs-5">
        			<li class="list-inline-item me-3"><a class="btn-facebook-share" title="Facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank"><i class="fab fa-facebook"></i></a></li>
        			<li class="list-inline-item me-3"><a class="btn-twitter-share" title="Twitter" href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ e( $response->title ) }}" data-url="{{ url()->current() }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
        			<li class="list-inline-item me-3"><a class="btn-pinterest-share" title="Pinterest" href="//www.pinterest.com/pin/create/button/?url={{ url()->current() }}&media={{url('files/preview/'.$stock_resolution, $stock_name)}}&description={{ e( $response->title ) }}" target="_blank"><i class="fab fa-pinterest"></i></a></li>
              <li class="list-inline-item"><a class="btn-whatsapp-share" title="Whatsapp" href="whatsapp://send?text={{ url()->current() }}" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
             </ul>
          </div>
        </div>
      </div>
    </div>
  </div><!-- thanks to author sharing -->
  @endif

@auth
<div class="modal fade" id="collections" tabindex="-1" role="dialog" aria-hidden="true">
     		<div class="modal-dialog modal-fullscreen-sm-down">
     			<div class="modal-content">
     				<div class="modal-header border-0">
				        <h5 class="modal-title text-center" id="myModalLabel">
				        	{{ trans('misc.add_collection') }}
                </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				     </div><!-- Modal header -->

			<div class="modal-body">

			<div class="collectionsData">
				@if ($collections->count() != 0)
				    @foreach ($collections as $collection )

				     @php

				     $collectionImages = $collection->collectionImages->where('images_id', $response->id)->where('collections_id', $collection->id)->first();

					 if ($collectionImages) {
						$checked = 'checked="checked"';
						} else {
							$checked = null;
						}
				     @endphp

				     	<div class="form-check mb-1">
                <input class="no-show form-check-input addImageCollection" data-image-id="{{$response->id}}" data-collection-id="{{$collection->id}}" name="checked" {{$checked}} type="checkbox" value="true" id="collectionCheck{{$collection->id}}">
							<label class="form-check-label text-overflow" for="collectionCheck{{$collection->id}}">
                {{$collection->title}}
							</label>
						</div>

				    @endforeach

				    @else
				    	<div class="d-block text-center no-collections mb-1 p-3 bg-warning rounded"><i class="bi bi-exclamation-circle me-1"></i> {{ trans('misc.no_have_collections') }}</div>
				    @endif

          </div><!-- collection data -->

          <small class="note-add @if ($collections->count() == 0) display-none @endif"><i class="bi bi-info-circle me-1"></i> {{ trans('misc.note_add_collections') }}</small>

          <span class="label label-success display-none d-block response-text"></span>


                 <!-- form start -->
			    <form method="POST" action="" enctype="multipart/form-data" id="addCollectionForm" class="mt-3">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
			    	<input type="hidden" name="image_id" value="{{ $response->id }}">

                 <!-- Start Form Group -->
                 <div class="form-floating mb-2">
                  <input type="text" class="form-control" name="title" id="titleCollection" id="titleCollection" placeholder="{{ trans('admin.title') }}">
                  <label for="titleCollection">{{ trans('admin.title') }}</label>
                </div>

                <div class="form-check form-switch">
                <input class="form-check-input radio-bws" name="type" type="checkbox" id="flexSwitchCheckDefault">
                <label class="form-check-label" for="flexSwitchCheckDefault">{{ trans('misc.private') }}</label>
              </div>

            <!-- Alert -->
            <div class="alert alert-danger py-2 display-none" id="dangerAlert">
							<ul class="list-unstyled m-0" id="showErrors"></ul>
						</div><!-- Alert -->

                 <div class="d-block text-center">
                 	<button type="submit" class="btn btn-custom" id="addCollection"><i></i> {{ trans('misc.create_collection') }}</button>
                 </div>
               </form>

				      </div><!-- Modal body -->
     				</div><!-- Modal content -->
     			</div><!-- Modal dialog -->
     		</div><!-- Modal -->
      @endauth

<section class="section section-sm">
  <div class="container-custom container pt-5">
    <div class="row">
  <!-- Col MD -->
  <div class="col-md-9">

  	@if ($response->status == 'pending')
  	<div class="alert alert-warning" role="alert">
  			<i class="bi bi-exclamation-triangle-fill me-1"></i> {{ trans('misc.pending_approval') }}
  		</div>
  		@endif

  @if (session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
   <i class="bi-exclamation-triangle me-1"></i> {{ session('error') }}

  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    <i class="bi bi-x-lg"></i>
  </button>
  </div>
@endif

	<div class="text-center mb-3">
    <div style="margin: 0 auto;">


     @if(count($thumbnails) < 2 && empty($response->vimeo_link))
        {{ renderThumbnail($thumbnails[0], $settings) }}
    @else
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="false">
          <!-- Indicators -->
        
          <!-- Wrapper for slides -->
          <div class="carousel-inner" role="listbox">
              <?php if(!empty($response->vimeo_link)):
                $video_width = $response->video_dimension == '16:9' ? '100%' : '400px';
            ?>
            <div class="item active">
                <div style="width: <?php echo $video_width; ?>;margin:0 auto;">
                  {{ renderEmbedVideo($response) }}
                </div>
            </div>
            <?php endif; ?>
            
            @foreach ($thumbnails as $k => $url )
            <div class="item {{ $k === 0 && empty($response->vimeo_link) ? 'active' : '' }}">
                {{ renderThumbnail($url, $settings) }}
            </div>
            @endforeach
            
            
          </div>
        
          <!-- Controls -->
          <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
    @endif











    </div>
	</div>

	<h1 class="text-break item-title pb-3">
	 {{ $response->title }}
	</h1>

  @if ($response->description != '')
  <p class="description none-overflow">
  	{{ $response->description }}
  </p>
  @endif
  
<!-- Start Block -->
<div class="d-block mb-4" style="text-align: left;">
    <div>
	<h5 class="fw-light">{{trans('misc.tags')}}</h5>
    <!--<span>Tags: </span>-->
  @php
	   $tags = explode(',', $response->tags);
	   $countTags = count($tags);
	 @endphp

  <div class="tags_wrap">
	  @for ($i = 0; $i < $countTags; ++$i)
      <a
        href="{{url('tags', str_replace(' ', '_', trim($tags[$i]))) }}"
        class="btn btn-sm bg-white border e-none btn-category mb-2 cb_tag {{ $i > 3 ? 'hide_tag' : '' }}"
      >
        {{ $tags[$i] }}
      </a>
	  @endfor

    @if($countTags > 3)
    <a
      href="#"
      class="btn btn-sm bg-white border e-none btn-category mb-2 show_hidden_tags"
    >
      {{ $countTags - 4 }}+
    </a>
    @endif
    </div>
  </div>
    <div><small>Click on the first tag (e.g., id1309) to see the same design in
other sizes /formats.</small></div>
</div><!-- End Block -->

@if ($images->count() != 0)
<!-- Start Block -->
<div class="d-block mb-4">
	<h5 class="fw-light">{{trans('misc.similar_photos')}}</h5>
	<div id="imagesFlex" class="flex-images d-block margin-bottom-40">
		@include('includes.images')
		</div>
</div><!-- End Block -->
@endif


<!-- Start Block -->
<div class="d-block mb-5">

  @if ($response->comments()->count() != 0 || auth()->check() && $settings->comments)
    <h5 class="fw-light mb-3">{{trans('misc.comments')}} (<span id="totalComments">{{ number_format($response->comments()->count()) }}</span>)</h5>
  @endif

	@if( auth()->check() && $response->status == 'pending' )
	<div class="alert alert-warning" role="alert">
			<i class="glyphicon glyphicon-info-sign me-1"></i>  {{ trans('misc.pending_approval') }}
		</div>
		@endif

@if (auth()->check() && $response->status == 'active' && $settings->comments)
	<div class="media mb-5">
            <span class="float-start me-2">
                <img alt="Image" src="{{ Storage::url(config('path.avatar').auth()->user()->avatar) }}" class="media-object rounded-circle" width="50">
            </span>

            <div class="media-body">
            <form action="{{ url('comment/store') }}" method="post" id="commentsForm">
            	<div class="form-group text-form mb-2">
            		<input type="hidden" name="image_id" value="{{ $response->id }}" />
            		<input type="hidden" name="_token" value="{{ csrf_token() }}">
            		<textarea name="comment" rows="3" required="required" min="2" id="comments" class="form-control textarea-comments mentions-textarea"></textarea>
            	</div>

            	<!-- Alert -->
            <div class="alert alert-danger alert-small display-none" id="dangerAlertComments">
							<ul class="list-unstyled m-0" id="showErrorsComments"></ul>
						</div><!-- Alert -->

            <div class="form-group ">
            	<button type="submit" class="btn btn-custom" id="commentSend">{{ trans('auth.send') }}</button>
            </div>
            </form>
          </div><!-- media body -->
 </div><!-- media -->
 @endif

	<div class="gridComments" id="gridComments">
		@include('includes.comments')
	</div><!-- gridComments -->

</div><!-- End Block -->

 </div><!-- /COL MD -->

 <div class="col-md-3">

 	@if (auth()->check() &&  isset($response->user()->id) && auth()->id() == $response->user()->id)
 	<div class="row mb-3">

			<div class="col-md-12">
				<a class="btn btn-sm bg-white border e-none btn-category d-block mb-2" href="{{ url('edit/template',$response->id) }}">{{trans('admin.edit')}}</a>
			</div>
			<div class="col-md-12">
        <form method="POST" action="{{ url('delete/template', $response->id) }}" accept-charset="UTF-8" class="d-inline">
          @csrf
          <button type="button" class="btn btn-sm bg-white border e-none btn-category text-danger d-block w-100" id="deletePhoto">
              <i class="bi bi-trash me-1 "></i> {{trans('admin.delete')}}
            </button>
	        	</form>
			</div>
		</div>
		@endif

 	<!-- Start Panel -->
 	<div class="panel panel-default mb-4">
	  <div class="panel-body">
	    <div class="media none-overflow">
			  <div class="float-start me-2">
			    <a href="{{url($response->user()->username)}}">
			      <img class="media-object rounded-circle" src="{{Storage::url(config('path.avatar').$response->user()->avatar)}}" width="60" height="60" >
			    </a>
			  </div>
			  <div class="media-body">
			  	<a href="{{url($response->user()->username)}}" class="text-dark">
			    	<h5 class="m-0">{{ $response->user()->name ?: $response->user()->username}}</h5>
			    </a>
			    <small class="d-block text-muted">{{ number_format(User::totalImages( $response->user()->id))}} {{trans_choice('misc.images_plural', User::totalImages( $response->user()->id ))}}</small>

          <p class="mt-2">
			    	@if (auth()->check() && $response->user()->id != auth()->id())
						<button type="button" class="btn btn-sm @if ($activeFollow) btn-custom  @else btn-outline-custom @endif btn-follow me-1 btnFollow {{ $activeFollow }}" data-id="{{ $response->user()->id }}" data-follow="{{ trans('users.follow') }}" data-following="{{ trans('users.following') }}">
	    			 			<i class="bi bi{{ $icoFollow }} me-1"></i> {{ $textFollow }}
	    			 		</button>
	    			 	@endif

	    			 	@if (auth()->check() && $response->user()->id != auth()->id() && $response->user()->paypal_account != '' || auth()->guest()  && $response->user()->paypal_account != '')
	    			 	<button type="button" class="btn btn-sm bg-white border e-none btn-category showTooltip" id="btnFormPP" title="{{trans('misc.buy_coffee')}}">
	    			 			<i class="bi bi-paypal" style="color: #003087"></i> @guest {{trans('misc.coffee')}} @endguest
	    			 		</button>
	    			 		@endif
			    </p>
			  </div>
			</div>
	  </div>
	</div><!-- End Panel -->

@if (auth()->check() && $response->status == 'active')
<div class="row mb-4">

	<!-- col-xs-6 -->
	<div class="col-6" style="border-right: 1px solid #e3e3e3;">
	@if (auth()->check())
		<a href="#" class="btnLike likeButton {{$statusLike}}" data-id="{{$response->id}}" data-like="{{trans('misc.like')}}" data-unlike="{{trans('misc.unlike')}}">
			<h3 class="d-block text-center margin-top-10"><i class="{{$icoLike}}"></i></h3>
			<small class="d-block text-center text-muted textLike">{{$textLike}}</small>
		</a>
		@endif
	</div><!-- col-xs-6 -->

		 <!-- col-xs-6 -->
		<div class="col-6">
		@if (auth()->check())
			<a href="#" class="btn-collection" data-bs-toggle="modal" data-bs-target="#collections">
				<h3 class="d-block text-center margin-top-10"><i class="bi bi-plus-square"></i></h3>
			    <small class="d-block text-center text-muted">{{trans('misc.collection')}}</small>
		    </a>
		    @endif
		</div><!-- col-xs-6 -->
</div>
@endif

	<!-- Start Panel -->
 	<div class="card mb-4">
	  <div class="card-body p-0">
	  		<ul class="list-stats list-inline">
	    	<li>
	    		<h5 class="d-block text-center m-0"><i class="bi bi-eye me-1"></i> <small class="text-muted">{{Helper::formatNumber($response->visits()->count())}}</small></h5>
	    	</li>

	    	<li>
	    		<h5 class="d-block text-center m-0"><i class="bi bi-heart me-1"></i> <small id="countLikes" class="text-muted">{{Helper::formatNumber($response->likes()->count())}}</small></h5>
	    	</li>

	    	<li>
	    		<h5 class="d-block text-center m-0"><i class="bi bi-download me-1"></i> <small class="text-muted">{{Helper::formatNumber($response->downloads()->count())}}</small></h5>
	    	</li>

	    </ul>
	  </div>
	</div><!-- End Panel -->

	@if ($response->featured == 'yes')
	<!-- Start Panel -->
	<div class="card mb-4">
		<div class="card-body">
	<i class="bi bi-award me-1"></i> <span class="text-muted">{{trans('misc.featured_on')}} </span>
	<strong>{{ Helper::formatDate($response->featured_date) }}</strong>
		</div>
	</div><!-- End card -->
	@endif

  <!-- btn-group -->
	<div class="d-block mb-3">

    @if ($response->item_for_sale == 'free'
        || auth()->check()
        && auth()->id() == $response->user_id
        && $response->item_for_sale == 'free'
        )
        <form action="{{url('download/stock', $token)}}" method="post">

          @csrf

          @guest
            @if ($settings->downloads == 'all')
                @captcha
            @endif
          @endguest

      <!-- btn-free -->
  		<button type="submit" class="btn btn-lg btn-custom w-100 d-block @if ($settings->downloads == 'all' || $settings->downloads == 'users' && auth()->check()) btnDownload downloadableButton @endif" id="downloadBtn">
  			<i class="bi bi-cloud-arrow-down me-1"></i> {{trans('misc.download')}}
  			</button>
        <!-- btn-free -->
        </form>

   @elseif (auth()->check()
        && auth()->id() != $response->user_id
        && auth()->user()->getSubscription()
        && auth()->user()->downloads != 0
        )

     <form action="{{url('subscription/stock', $token)}}" method="post" id="formBuy">
         @csrf

       <div class="position-relative mb-3">

         <div class="list-group mx-0">
           @foreach( $stockImages as $stock )
           <?php
           switch( $stock->type ) {
             case 'small':
        				$_size = trans('misc.s');
                $imageType = trans('misc.small_photo');
        				break;
        			case 'medium':
        				$_size = trans('misc.m');
                $imageType = trans('misc.medium_photo');
        				break;
        			case 'large':
        				$_size = trans('misc.l');
                $imageType = trans('misc.large_photo');
        				break;
              case 'vector':
                $_size = trans('misc.v');
                $imageType = trans('misc.vector_graphic');
                  break;
               break;
         }
          ?>
           <label class="list-group-item d-flex gap-2 c-pointer">
             <input class="form-check-input flex-shrink-0" type="radio" @if ($stock->type == 'small') checked @endif name="type" id="radio{{$stock->type}}" value="{{$stock->type}}">
             <span>
               <span class="badge bg-custom me-1">{{ $_size }}</span> {{$stock->type == 'vector' ? trans('misc.vector_graphic').' ('.strtoupper($stock->extension).')' : $stock->resolution}}
               <small class="d-block text-muted">{{trans('misc.file_size')}}: {{$stock->size}}</small>
             </span>
           </label>
           @endforeach

         </div>
       </div><!-- form-group -->

       <!-- btn-sale -->
   		<button class="btn btn-custom btn-lg d-block w-100 downloadableButton" data-type="small" id="downloadBtn" type="submit">

      <i class="bi bi-cloud-arrow-down me-1"></i> {{trans('misc.download')}}

   		</button>
       <!-- btn-sale -->
     </form>

    @else

    <form action="{{url('purchase/stock', $token)}}" method="post" id="formBuy">

      @csrf


      <!-- btn-sale -->
  		<button class="btn btn-custom btn-lg d-block w-100" data-type="small" id="downloadBtn" @if (auth()->check() && auth()->id() != $response->user_id) data-bs-toggle="modal" data-bs-target="#checkout" type="button" @else type="submit" @endif>

     @if (auth()->check() && auth()->id() == $response->user_id)
       <i class="bi-cloud-arrow-down me-1"></i> {{trans('misc.download')}}
     @else
       <i class="bi bi-cart2 me-1"></i>
       {{trans('misc.buy')}}

       <span id="priceItem">{{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span id="itemPrice">{{$itemPrice}}</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }} <small class="sm-currency-code">{{$settings->currency_code}}</small></span>
     @endif

  		</button>
      <!-- btn-sale -->
    </form>

    @endif

    @if (auth()->check() && ! auth()->user()->getSubscription()
      && $response->item_for_sale == 'sale'
      && auth()->id() != $response->user_id
      || auth()->guest() && $response->item_for_sale == 'sale')
      @if (Plans::whereStatus('1')->count() != 0)
        <a class="btn btn-success btn-lg d-block w-100 mt-2 arrow" href="{{ url('pricing') }}">
        {{ __('misc.subscribe_save') }}
      </a>
     @endif
    @endif

    @if (auth()->check()
        && auth()->user()->getSubscription()
        && $response->item_for_sale == 'sale'
        && auth()->id() != $response->user_id
        && auth()->user()->downloads != 0)
      <small class="d-block w-100 text-center mt-1 text-success fst-italic lh-sm">
      <i class="bi-check2 me-1"></i>   {{ __('misc.included_your_subscription') }}
      </small>
    @elseif (auth()->check()
        && auth()->user()->getSubscription()
        && $response->item_for_sale == 'sale'
        && auth()->id() != $response->user_id
        && auth()->user()->downloads == 0)
      <small class="d-block w-100 text-center mt-1 text-danger fst-italic lh-sm">
      <i class="bi-exclamation-triangle-fill me-1"></i> {{ __('misc.reached_download_limit_plan') }}
      </small>
    @endif

    @guest
      @if ($settings->downloads == 'all' && $response->item_for_sale == 'free')
        <small class="d-block w-100 text-center mt-2 lh-sm fs-small text-muted">
          {{trans('misc.protected_recaptcha')}}
          <a href="https://policies.google.com/privacy" target="_blank">{{trans('misc.privacy')}}</a> - <a href="https://policies.google.com/terms" target="_blank">{{trans('misc.terms')}}</a>
        </small>
      @endif
    @endguest

	</div><!-- End btn-group -->

	<!-- Start Panel -->
	<div class="card">
		<div class="card-body">
			<h6><i class="fab fa-creative-commons me-1"></i> {{trans('misc.license_and_use')}}</h6>
			<?php foreach($license as $line) { ?>
			<small class="d-block text-muted"><i class="bi bi-check2 me-1"></i> {{$line}}</small>
			<?php } ?>
		</div>
	</div><!-- End Panel -->
	<div class=" mb-4">
	    <small class="text-center d-block mt-1">
            <a href="{{ URL('page/terms-of-service') }}" target="_blank">View license details <i class="bi bi-arrow-up-right"></i></a>
        </small>
	</div>

	@if ($response->status == 'active')
		<!-- Start Panel -->
	<div class="card mb-4">
		<div class="card-body">
			<h6 class="float-start m-0" style="line-height: inherit;"><i class="bi bi-share me-1"></i> {{trans('misc.share')}}</h6>

		<ul class="list-inline float-end m-0 fs-5">
			<li class="list-inline-item"><a class="btn-facebook-share" title="Facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank"><i class="fab fa-facebook"></i></a></li>
			<li class="list-inline-item"><a class="btn-twitter-share" title="Twitter" href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ e( $response->title ) }}" data-url="{{ url()->current() }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
			<li class="list-inline-item"><a class="btn-pinterest-share" title="Pinterest" href="//www.pinterest.com/pin/create/button/?url={{ url()->current() }}&media={{url('files/preview/'.$stock_resolution, $stock_name)}}&description={{ e( $response->title ) }}" target="_blank"><i class="fab fa-pinterest"></i></a></li>
      <li class="list-inline-item"><a class="btn-whatsapp-share" title="Whatsapp" href="whatsapp://send?text={{ url()->current() }}" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
     </ul>
		</div>
	</div><!-- End card -->
	@endif

	@if ($response->exif != '' || $response->camera != '')
	<!-- Start Panel -->
	<div class="card mb-4">
		<div class="card-body">
			<h6><i class="bi bi-camera me-1"></i> {{trans('misc.exif_data')}}</h6>

			@if ($response->camera != '')
			<small class="d-block text-muted">{{trans('misc.photo_taken_with')}}</small>
			<small class="d-block text-muted"><a href="{{url('cameras', $response->camera)}}">{{$response->camera}}</a></small>
			@endif

			<small class="d-block text-muted wordSpacing">{{$response->exif}}</small>

		</div>
	</div><!-- End card -->
	@endif


	@if ($response->colors != '')

	@php
   $colors = explode(',', $response->colors);
   $count_colors = count( $colors );
  @endphp

	<!-- Start Panel -->
	<div class="card mb-4">
		<div class="card-body">
			<h6><i class="bi bi-droplet me-1"></i> {{trans('misc.color_palette')}}</h6>

			@for ($c = 0; $c < $count_colors; ++$c)
		   	<a title="#{{$colors[$c]}}" href="{{url('colors') }}/{{$colors[$c]}}" class="colorPalette showTooltip" style="background-color: {{ '#'.$colors[$c] }};"></a>
		   	@endfor
		</div>
	</div><!-- End card -->
	@endif


<ul class="list-group mb-4">
       <li class="list-group-item"><i class="bi bi-info-circle me-1"></i> <strong>{{trans('misc.details')}}</strong></li>

    <li class="list-group-item">{{trans('misc.published')}} <small class="float-end text-muted">{{Helper::formatDate($response->date)}}</small></li>
	  <li class="list-group-item">{{trans('misc.image_type')}} <small class="float-end text-muted">{{strtoupper($response->extension)}} </small></li>
	  <!--<li class="list-group-item">{{trans('misc.resolution')}} <small class="float-end text-muted">{{$stock_resolution}}</small></li>-->
	  <li class="list-group-item">{{trans('misc.category')}}
      <small class="float-end text-muted">
      <a href="{{url('category',$response->category->slug)}}" title="{{ Lang::has('categories.' . $response->category->slug) ? __('categories.' . $response->category->slug) : $response->category->name }}">
        {{ Lang::has('categories.' . $response->category->slug) ? str_limit(__('categories.' . $response->category->slug), 18, '...') : str_limit($response->category->name, 18, '...') }}
      </a>
      </small>
    </li>
	  <li href="#" class="list-group-item">{{trans('misc.file_size')}} <small class="float-end text-muted">{{$stock_size}}</small></li>
	</ul>

	<!-- Start Panel -->
	<div class="card">
		<div class="card-body">
			<h6><i class="fab fa-creative-commons me-1"></i> Use Cases</h6>
			<?php foreach($usecase as $line) { ?>
			<small class="d-block text-muted"><i class="bi bi-check2 me-1"></i> {{$line}}</small>
			<?php } ?>
		</div>
	</div><!-- End Panel -->
	<div class=" mb-4">
	    <small class="text-center d-block mt-1">
            <a href="{{ URL('page/help') }}" target="_blank">How to edit template <i class="bi bi-arrow-up-right"></i></a>
        </small>
	</div>
@if (auth()->check())
<div class="modal fade" id="reportImage" tabindex="-1" role="dialog" aria-hidden="true">
     		<div class="modal-dialog modal-sm">
     			<div class="modal-content">
     				<div class="modal-header border-0">
				        <h5 class="modal-title text-center" id="myModalLabel">
				        	{{ trans('misc.report_photo') }}
                </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				     </div><!-- Modal header -->

				  <div class="modal-body">

				    <!-- form start -->
			    <form method="POST" action="{{ url('report/template') }}" enctype="multipart/form-data" id="formReport">
			    	<input type="hidden" name="_token" value="{{ csrf_token() }}">
			    	<input type="hidden" name="id" value="{{ $response->id }}">
				    <!-- Start Form Group -->

            <div class="form-floating mb-3">
              <select name="reason" class="form-select" id="input-reason">
                <option value="copyright">{{ trans('admin.copyright') }}</option>
                <option value="privacy_issue">{{ trans('admin.privacy_issue') }}</option>
                <option value="violent_sexual_content">{{ trans('admin.violent_sexual_content') }}</option>
              </select>
              <label for="input-reason">{{ trans('admin.reason') }}</label>
            </div>

                <button type="submit" class="btn btn-custom float-end" id="reportPhoto">{{ trans('misc.report_photo') }}</button>

                    </form>

				      </div><!-- Modal body -->
     				</div><!-- Modal content -->
     			</div><!-- Modal dialog -->
     		</div><!-- Modal -->
     		@endif

@if (auth()->check() && auth()->id() != $response->user()->id && $response->status == 'active')
	<div class="d-block text-center mt-2">
		<a href="#" data-bs-toggle="modal" data-bs-target="#reportImage" class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i> {{ trans('misc.report_photo') }}</a>
	</div>
	@endif

	@if (isset($settings->google_adsense))
	<div class="margin-top-20">
	   {!! $settings->google_adsense !!}
	</div>
	@endif

 </div><!-- /COL MD -->
</div><!-- row -->
 </div><!-- container wrap-ui -->

 @if (auth()->check() && $response->user()->id != auth()->id() && $response->user()->paypal_account != '' || auth()->guest()  && $response->user()->paypal_account != '')
 <form id="form_pp" name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post"  style="display:none">
    <input type="hidden" name="cmd" value="_donations">
    <input type="hidden" name="return" value="{{url('template',$response->id)}}">
    <input type="hidden" name="cancel_return"   value="{{url('template',$response->id)}}">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="item_name" value="{{trans('misc.support').' @'.$response->user()->username}} - {{$settings->title}}" >
    <input type="hidden" name="business" value="{{$response->user()->paypal_account}}">
    <input type="submit">
</form>
@endif

@if (auth()->check() && $response->user()->id != auth()->id() && $response->item_for_sale == 'sale')
  <div class="modal fade" tabindex="-1" id="checkout">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
      <div class="modal-content">
        <div class="modal-body p-lg-4">
          <h5 class="mb-3">
            <i class="bi bi-cart2 me-1"></i> {{ __('misc.checkout') }}

            <span class="float-end c-pointer" data-bs-dismiss="modal" aria-label="Close">
              <i class="bi bi-x-lg"></i>
            </span>
          </h5>
          <div class="container">
            <div class="row">

              <div class="col-md-6 ps-0">
                <div class="mb-3">
                  <strong>{{ __('misc.payments_options') }}</strong>
                </div>

                <form method="post" action="{{ url('buy/stock', $token) }}" class="d-inline"
                  id="formSendBuy">
                  @csrf

                  <input type="hidden" id="licenseOnModal" name="license" value="regular">
                  <input type="hidden" id="typeOnModal" name="type" value="small">
                  <input type="hidden" name="token" value="{{ $token }}">

                  <input type="hidden" name="urlCancel" value="{{ url()->current() }}" />

                  @foreach (PaymentGateways::where('enabled', '1')->orderBy('type', 'DESC')->get() as $payment)
                    <div class="form-check custom-radio mb-2">
                      <input name="payment_gateway" value="{{ $payment->id }}"
                        id="payment_radio{{ $payment->id }}"
                        @if ($paymentsGatewaysEnabled == 1 && auth()->user()->funds == 0.0) checked @endif
                        class="form-check-input radio-bws" type="radio">
                      <label class="form-check-label" for="payment_radio{{ $payment->id }}">
                        <span><img class="me-1 rounded"
                            src="{{ url('public/img/payments', $payment->logo) }}"
                            width="20" /> <strong>{{ $payment->name }}</strong></span>
                        <small class="w-100 d-block">
                          @if ($payment->type == 'card')
                            {{ trans('misc.debit_credit_card') }}
                          @endif

                          @if ($payment->type == 'cryptocurrency')
                            {{ trans('misc.cryptocurrency') }}
                          @endif

                          @if ($payment->name == 'PayPal')
                            {{ trans('misc.paypal_info') }}
                          @endif
                        </small>
                      </label>
                    </div>
                  @endforeach

                  <div class="form-check custom-radio mb-3">
                    <input name="payment_gateway" @if (auth()->user()->funds == 0.0) disabled @endif
                      value="wallet" id="wallet" class="form-check-input radio-bws"
                      type="radio">
                    <label class="form-check-label" for="wallet">
                      <span><img class="me-1 rounded"
                          src="{{ url('public/img/payments/wallet.png') }}" width="20" />
                        <strong>{{ __('misc.wallet') }}</strong></span>
                      <small class="w-100 d-block">
                        {{ __('misc.available_balance') }}:
                        <strong>{{ Helper::amountFormatDecimal(auth()->user()->funds) }}</strong>
                      </small>
                    </label>
                  </div>
              </div>
              <div class="col-md-6 ps-0">

                <div class="mb-1">
                  <strong>{{ __('misc.order_summary') }}</strong>
                </div>

                <ul class="list-group list-group-flush mb-3">

                  <li class="list-group-item py-1 px-0">
                    <div class="row">
                      <div class="col">
                        <span id="summaryImage">{{ __('misc.small_photo') }}</span>
                        <small id="summaryLicense"
                          class="d-block w-100">{{ __('misc.license_regular') }}</small>
                      </div>

                      <div class="col-auto">
                        <img class="rounded"
                          src="{{ Helper::getThumbUrl($response->thumbnail) }}"
                          style="max-height: 40px;" />
                      </div>

                    </div>
                  </li>

                  <li class="list-group-item py-1 px-0">
                    <div class="row">
                      <div class="col">
                        <small>{{ __('misc.subtotal') }}:</small>
                      </div>
                      <div class="col-auto">
                        {{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<small
                          class="subtotal font-weight-bold">{{ number_format($itemPrice, 2) }}</small>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }}
                      </div>
                    </div>
                  </li>

                  @if (auth()->user()->isTaxable()->count())

                    @php
                      $number = 0;
                    @endphp

                    @foreach (auth()->user()->isTaxable() as $tax)
                      @php
                        $number++;
                      @endphp
                      <li class="list-group-item py-1 px-0 isTaxable">
                        <div class="row">
                          <div class="col">
                            <small>{{ $tax->name }} {{ $tax->percentage }}%:</small>
                          </div>
                          <div class="col-auto percentageAppliedTax{{ $number }}"
                            data="{{ $tax->percentage }}">
                            <small class="font-weight-bold">
                              {{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span
                                class="amount{{ $number }}">{{ Helper::calculatePercentage($itemPrice, $tax->percentage) }}</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }}
                            </small>
                          </div>
                        </div>
                      </li>
                    @endforeach
                  @endif

                  <li class="list-group-item py-1 px-0">
                    <div class="row">
                      <div class="col">
                        <small class="fw-bold">{{ __('misc.total') }}:</small>
                      </div>
                      <div class="col-auto fw-bold">
                        <small>{{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span
                            id="total">{{ Helper::amountGross($itemPrice) }}</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }}
                          {{ $settings->currency_code }}</small>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="row" style="margin-top: 15px;">
                      @if(!$coupon_applied)
                      <div class="col-8">
                        <input id="applyCouponCode" type="text" class="form-control" value="" placeholder="Enter coupon code" name="coupon_code">
                      </div>
                      <div class="col-4">
                        <a id="sendCouponCode" href="#" class="btn btn-dark" style="width: 100%;">Apply</a>
                      </div>
                      @else
                      <div class="col-8">
                        <div class="coupon_applied">Coupon Applied: {{ $coupon_applied }}</div>
                      </div>
                      <div class="col-4">
                        <a href="{{ Request::url() }}?remove_coupon_code" class="btn btn-outline-danger" style="width: 100%;">Remove</a>
                      </div>
                      @endif
                    </div>
                  </li>
                </ul>

                <div class="alert alert-danger py-2 display-none" id="errorPurchase">
                  <ul class="list-unstyled m-0" id="showErrorsPurchase"></ul>
                </div>

                <button type="submit" @if ($paymentsGatewaysEnabled == 1 && auth()->user()->funds == 0.0) @else disabled @endif
                  class="btn btn-success w-100" id="payButton"><i></i> {{ __('misc.pay') }}</button>
                <div class="w-100 d-block text-center">
                  <button type="button" class="btn btn-link e-none text-decoration-none text-reset"
                    data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                </div>
              </div>

              </form>
            </div><!-- row -->
          </div><!-- container -->


        </div><!-- modal-body -->
      </div><!-- modal-content -->
    </div><!-- modal-dialog -->
  </div><!-- modal -->
@endif


</section>
@endsection

@section('javascript')
<script src="{{ asset('public/js/lazysizes.min.js') }}" async=""></script>

@if (auth()->check() && $response->user()->id != auth()->id() && $response->item_for_sale == 'sale')
  <script src="{{ asset('public/js/checkout.js') }}?v={{$settings->version}}"></script>

  <script src='https://js.paystack.co/v1/inline.js'></script>
  <script src='https://checkout.razorpay.com/v1/checkout.js'></script>
@endif

<script type="text/javascript">

const lightbox = GLightbox({
					    touchNavigation: true,
					    loop: false,
					    closeEffect: 'fade'
					});

$autor = false;

$('#imagesFlex').flexImages({ maxRows: 3, truncate: true });

$('#btnFormPP').click(function(e) {
	$('#form_pp').submit();
});

@if (session('noty_error'))
    		swal({
    			title: "{{ trans('misc.error_oops') }}",
    			text: "{{ trans('misc.already_sent_report') }}",
    			type: "error",
    			confirmButtonText: "{{ trans('users.ok') }}"
    			});
   		 @endif

   		 @if (session('noty_success'))
    		swal({
    			title: "{{ trans('misc.thanks') }}",
    			text: "{{ trans('misc.send_success') }}",
    			type: "success",
    			confirmButtonText: "{{ trans('users.ok') }}"
    			});
   		 @endif

  @if (auth()->check())

  $("#reportPhoto").click(function(e) {
  	var element     = $(this);
	e.preventDefault();
  	 element.attr({'disabled' : 'true'});

  	 $('#formReport').submit();

  });

  // Comments Delete
$(document).on('click','.deleteComment',function () {

	var $id = $(this).data('id');

	$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
			});

		swal(
			{   title: "{{trans('misc.delete_confirm')}}",
			  type: "warning",
			  showLoaderOnConfirm: true,
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			   confirmButtonText: "{{trans('misc.yes_confirm')}}",
			   cancelButtonText: "{{trans('misc.cancel_confirm')}}",
			    closeOnConfirm: false,
			    },
			    function(isConfirm){
			    	 if (isConfirm) {

			element = $(this);

			element.removeClass('deleteComment');

			$.post("{{url('comment/delete')}}",
			{ comment_id: $id },
			function(data){
				if (data.success == true) {
					window.location.reload();
				}

			},'json');

			   }
	       });
		});

  // Likes Comments
		$(document).on('click','.likeComment', function () {

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
			});

			element = $(this);

			$.post("{{url('comment/like')}}",
			{ comment_id: $(this).data('id')
			}, function(data){
				if (data.success == true) {
					if (data.type == 'like') {
            element.addClass('activeLikeComment');
						element.html('<i class="bi bi-heart-fill"></i>');
						element.parent('.d-block').find('.count').html(data.count).fadeIn();
						element.parent('.d-block').find('.like-small').fadeIn();
						element.blur();

					} else if (data.type == 'unlike' ) {
            element.removeClass('activeLikeComment');
						element.html('<i class="bi bi-heart"></i>');

					if( data.count == 0 ) {
						element.parent('.d-block').find('.count').html(data.count).fadeOut();
						element.parent('.d-block').find('.like-small').fadeOut();
					} else {
						element.parent('.d-block').find('.count').html(data.count).fadeIn();
						element.parent('.d-block').find('.like-small').fadeIn();
					}

						element.blur();
					}
				} else {
					window.location.reload();
				}

				if (data.session_null) {
					window.location.reload();
				}
			},'json');
		});
  @endif

  @if (auth()->check() && auth()->id() == $response->user()->id)

  var $autor = true;

  // Delete Photo
	 $("#deletePhoto").click(function(e) {
	   	e.preventDefault();

	   	var element = $(this);
		  var form    = $(element).parents('form');

		element.blur();

		swal(
			{   title: "{{trans('misc.delete_confirm')}}",
			  type: "warning",
			  showLoaderOnConfirm: true,
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			   confirmButtonText: "{{trans('misc.yes_confirm')}}",
			   cancelButtonText: "{{trans('misc.cancel_confirm')}}",
			    closeOnConfirm: false,
			    },
			    function(isConfirm){
			    	 if (isConfirm) {
			    	 	form.submit();
			    	 	}
			    	 });
			 });
	@endif

  function scrollElement(element) {
  	var offset = $(element).offset().top;
  	$('html, body').animate({scrollTop:offset}, 500);
  };

	//<<---- PAGINATION AJAX
    $(document).on('click','.pagination a', function(e) {
			e.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			$.ajax({
				headers: {
        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
					url: '{{ url("/") }}/ajax/comments?photo={{$response->id}}&page=' + page


			}).done(function(data) {
				if (data) {

					scrollElement('#gridComments');

					$('.gridComments').html(data);

					jQuery(".timeAgo").timeago();

					$('[data-toggle="tooltip"]').tooltip();
				} else {
					$('.popout').addClass('popout-error').html(error).fadeIn('500').delay('5000').fadeOut('500');
				}
				//<**** - Tooltip
			});
		});//<<---- PAGINATION AJAX

    function toFixed(number, decimals) {
          var x = Math.pow(10, Number(decimals) + 1);
          return (Number(number) + (1 / x)).toFixed(decimals);
        }

        function taxes(price) {
          // Taxes
          var taxes = $('li.isTaxable').length;
          var totalTax = 0;

          for (var i = 1; i <= taxes; i++) {
            var percentage = $('.percentageAppliedTax'+i).attr('data');
            var valueFinal = (price * percentage / 100);
            $('.amount'+i).html(toFixed(valueFinal, 2));
            totalTax += valueFinal;
          }
          return (Math.round(totalTax * 100) / 100).toFixed(2);
        }

      $('#license').on('change', function() {

        var type  = $("#license option:selected").text();
        $price = $('#itemPrice').html();


        if ($(this).val() == 'regular') {
          $finalPrice = parseFloat($price/10);

          $('#itemPrice').html($finalPrice);
          $('.subtotal').html($finalPrice.toFixed(2));
        } else {
          $finalPrice = parseFloat($price*10);

          $('#itemPrice').html($finalPrice);
          $('.subtotal').html($finalPrice.toFixed(2));
        }

        // Total
        var total = (parseFloat($finalPrice) + parseFloat(taxes($finalPrice)));
        $('#total').html(total.toFixed(2));

        $('#licenseOnModal').val($(this).val());
        $('#summaryLicense').html(type);
      });

    $('.itemPrice').on('click', function() {

      var type  = $(this).attr("data-type");
      var amount  = $(this).attr("data-amount");
      var dataImage = $(this).attr('data-image');
      var buttonDownload = '<i class="fa fa-cloud-download me-1"></i> {{trans('misc.download')}}';
      var license = $('#license').val();

      $('#typeOnModal').val(type);
      $('#summaryImage').html(dataImage);

      if (license == 'regular') {
        var valueOriginal = {{$itemPrice}};
      } else {
        var valueOriginal = ({{$itemPrice}}*10);
      }

      var amountMedium = (valueOriginal * 2);
      var amountLarge = (valueOriginal * 3);
      var amountVector = (valueOriginal * 4);

      if (type == 'small') {

        if ($autor == false) {
          $('#downloadBtn').html('<i class="bi bi-cart2 me-1"></i> {{trans('misc.buy')}} <span id="priceItem">{{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span id="itemPrice">'+valueOriginal+'</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }} <small class="sm-currency-code">{{$settings->currency_code}}</small></span>')
          .attr('data-type', 'small');
          $('.subtotal').html(valueOriginal.toFixed(2));

          // Total
          var total = (parseFloat(valueOriginal) + parseFloat(taxes(valueOriginal)));
          $('#total').html(total.toFixed(2));
        }

      } else if (type == 'medium') {

        if ($autor == false) {
          $('#downloadBtn').html('<i class="bi bi-cart2 me-1"></i> {{trans('misc.buy')}} <span id="priceItem">{{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span id="itemPrice">'+amountMedium+'</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }} <small class="sm-currency-code">{{$settings->currency_code}}</small></span>')
          .attr('data-type', 'medium');
          $('.subtotal').html(amountMedium.toFixed(2));

          // Total
          var total = (parseFloat(amountMedium) + parseFloat(taxes(amountMedium)));
          $('#total').html(total.toFixed(2));
        }

      } else if (type == 'large') {

        if($autor == false) {
          $('#downloadBtn').html('<i class="bi bi-cart2 me-1"></i> {{trans('misc.buy')}} <span id="priceItem">{{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span id="itemPrice">'+amountLarge+'</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }} <small class="sm-currency-code">{{$settings->currency_code}}</small></span>')
          .attr('data-type', 'large');
          $('.subtotal').html(amountLarge.toFixed(2));

          // Total
          var total = (parseFloat(amountLarge) + parseFloat(taxes(amountLarge)));
          $('#total').html(total.toFixed(2));
        }

      }  else if (type == 'vector') {

        if($autor == false) {
          $('#downloadBtn').html('<i class="bi bi-cart2 me-1"></i> {{trans('misc.buy')}} <span id="priceItem">{{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span id="itemPrice">'+amountVector+'</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }} <small class="sm-currency-code">{{$settings->currency_code}}</small></span>')
          .attr('data-type', 'vector');
          $('.subtotal').html(amountVector.toFixed(2));

          // Total
          var total = (parseFloat(amountVector) + parseFloat(taxes(amountVector)));
          $('#total').html(total.toFixed(2));
        }
      }
    });

(function($) {
  $(document).ready(function() {
    $('#applyCouponCode').on('change', function() {
      var val = $(this).val();

      var href = '#';
      var current_link = window.location.href.split('?')[0];

      if(val != '') {
        href = current_link + '?add_coupon_code=' + val;
      }

      $('#sendCouponCode').attr('href', href);
    })

    @if(!empty($_GET['add_coupon_code']) || !empty($_GET['remove_coupon_code']))
    $('#checkout').modal('show');
    @endif
  })
}(jQuery))

</script>

@endsection
