<?php
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;

function renderVimeoEmbed($response)
{
  $vimeo_embed = 'https://player.vimeo.com/video/' . str_replace('https://vimeo.com/', '', $response->vimeo_link);
  ?>
  <div class="embed-responsive embed-responsive-<?php echo str_replace(':', 'by', $response->video_dimension); ?>">
    <iframe src="<?php echo $vimeo_embed; ?>?autoplay=1&muted=1" frameborder="0" allow="autoplay" allowfullscreen
      style="min-height: 200px;background:black;"></iframe>
  </div>
  <?php
}

function renderStreamableEmbed($response)
{
  $video_link = $response->vimeo_link;
  $video_embed = stristr($video_link, '/e/') ? $video_link : str_replace('streamable.com', 'streamable.com/e', $video_link);
  ?>
  <div style="width:100%;height:0px;position:relative;padding-bottom:136.667%;">
    <iframe src="<?php echo $video_embed; ?>" frameborder="0" width="100%" height="100%" allowfullscreen
      style="width:100%;height:100%;position:absolute;left:0px;top:0px;overflow:hidden;"></iframe>
  </div>
  <?php
}

function renderEmbedVideo($response)
{
  $video_link = $response->vimeo_link;
  if (stristr($video_link, 'vimeo.com')) {
    return renderVimeoEmbed($response);
  }

  if (stristr($video_link, 'streamable.com')) {
    return renderStreamableEmbed($response);
  }

  return null;
}

function redirect_now($url, $code = 302)
{
  try {
    \App::abort($code, '', ['Location' => $url]);
  } catch (\Exception $exception) {
    $previousErrorHandler = set_exception_handler(function () { });
    restore_error_handler();
    call_user_func($previousErrorHandler, $exception);
    die;
  }
}


function isCouponValid($coupon_code)
{
  if (empty($coupon_code))
    return false;

  $coupon = Coupon::where('code', $coupon_code)->first();

  $current_date = date('Y-m-d H:i:s');
  if (!$coupon || $current_date < $coupon->start_date || $current_date > $coupon->end_date) {
    return false;
  }

  return true;
}

function applyCouponToPrice($price, $coupon_code = null)
{
  if (empty($coupon_code))
    $coupon_code = couponApplied();

  if (!isCouponValid($coupon_code))
    return $price;

  $coupon = Coupon::where('code', $coupon_code)->first();

  if (!$coupon)
    return $price;

  if ($coupon->discount_type === 'fixed') {
    $discountedPrice = $price - $coupon->discount;
  } else {
    $discountedPrice = $price - ($price * ($coupon->discount / 100));
  }

  return $discountedPrice < $price ? max($discountedPrice, 0) : $price;
}

function removeCoupon()
{
  return Cookie::queue(Cookie::forget('cb_coupon_code'));
}

function applyCoupon($coupon)
{
  return Cookie::queue(Cookie::make('cb_coupon_code', $coupon, 1440));
}

function applyOrRemoveCoupon($current_url_path)
{
  // apply coupon
  if (!empty($_GET['add_coupon_code'])) {
    $code = $_GET['add_coupon_code'];

    if (isCouponValid($code)) {
      applyCoupon($code);

      $current_url_path .= '?coupon_applied';
    } else {
      $current_url_path .= '?invalid_coupon';
    }

    redirect_now($current_url_path);
  }

  // remove coupon
  if (isset($_GET['remove_coupon_code'])) {
    removeCoupon();
    $current_url_path .= '?coupon_removed';

    redirect_now($current_url_path);
  }
}

function couponApplied()
{
  $coupon = Cookie::get('cb_coupon_code');

  if ($coupon) {
    return isCouponValid($coupon) ? $coupon : false;
  }

  return false;
}
