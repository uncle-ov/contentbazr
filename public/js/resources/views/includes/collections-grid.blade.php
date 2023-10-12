<?php 
    function dawihGetCollectionThumbnailUrl($img) {
        $img = App\Models\Images::where('id', $img->images_id)->first();
        $thumbs = json_decode($img->thumbnail);
        
        $thumbnail = '/uploads/'.$thumbs[0]->file;
        $thumburl = Storage::disk('azure')->url($thumbnail);
        
        return $thumburl;
    }
?>

@foreach ($data as $collection)

@php
  $image = $collection->collectionImages()->take(3)->get();
  $totalImages = $collection->collectionImages()->count();
 @endphp

 <div class="col-md-4 mb-3 text-truncate">
   <a href="{{ url($collection->user()->username.'/collection', $collection->id) }}" class="position-relative text-decoration-none">
     <div class="wrap-collection">
       <div class="grid-collection">
       <div class="collection-1">
       @if (isset($image[0]))
         <img role="presentation" class="img-collection" src="{{ dawihGetCollectionThumbnailUrl($image[0]) }}?size=medium">
       @endif
       </div><!-- collection-1 -->

       <div class="collection-right">
         <div class="collection-2">
           @if (isset($image[1]))
             <img role="presentation" class="img-collection" src="{{ dawihGetCollectionThumbnailUrl($image[1]) }}?size=medium">
           @endif
         </div>

         <div class="collection-2">
           @if (isset($image[2]))
             <img role="presentation" class="img-collection" src="{{ dawihGetCollectionThumbnailUrl($image[2]) }}?size=medium">
           @endif
         </div>
       </div>

       </div><!-- grid-collection -->
     </div><!-- wrap-collection -->
     <span class="collection-title mb-1">
       @if ($collection->type == 'private') <i class="fa fa-lock me-1 padlock" data-bs-toggle="tooltip" data-bs-placement="top" title="{{trans('misc.private')}}"></i> @endif {{$collection->title}}
     </span>

     <small class="d-block w-100 text-muted">
       {{ $totalImages }} {{ trans_choice('misc.images_plural', $totalImages) }} - {{ trans('misc.by') }} <strong>{{$collection->user()->username}}</strong>
     </small>
   </a>
 </div><!-- col-3-->
@endforeach

@if ($data->count() != 0)
  <div class="container-paginator" id="linkPagination">
    {{ $data->links() }}
    </div>
  @endif
