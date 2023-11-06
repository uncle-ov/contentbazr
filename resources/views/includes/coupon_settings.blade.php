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

@if($coupon_applied)
<li class="list-group-item py-1 px-0">
  <div class="row">
    <div class="col">
      <small class="">Discount:</small>
    </div>
    <div class="col-auto">
      <small id="couponSavings">- {{ $settings->currency_position == 'left' ? $settings->currency_symbol : null }}<span
          id="total">{{ $discountSavings }}</span>{{ $settings->currency_position == 'right' ? $settings->currency_symbol : null }}
        {{ $settings->currency_code }}</small>
    </div>
  </div>
</li>
@endif

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
