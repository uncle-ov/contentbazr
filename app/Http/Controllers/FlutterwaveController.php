<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\Deposits;
use App\Models\Images;
use App\Models\User;
use App\Helper;
use App\Models\PaymentGateways;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use App\Models\Purchases;
use Mail;
use Carbon\Carbon;

class FlutterwaveController extends Controller
{
  use Traits\FunctionsTrait;

  public function __construct(AdminSettings $settings, Request $request)
  {
    $this->settings = $settings::first();
    $this->request = $request;
  }

  // Add funds
  public function show()
  {

    if (!$this->request->expectsJson()) {
      abort(404);
    }

    try {

      // Get Payment Gateway
      $payment = PaymentGateways::whereName('Flutterwave')->firstOrFail();

      $fee = $payment->fee;

      $taxes = $this->settings->tax_on_wallet ? ($this->request->amount * auth()->user()->isTaxable()->sum('percentage') / 100) : 0;

      $amountFixed = number_format($this->request->amount + ($this->request->amount * $fee / 100) + $taxes, 2, '.', '');

      //This generates a payment reference
      $reference = Flutterwave::generateReference();

      // Enter the details of the payment
      $data = [
        'payment_options' => 'card,banktransfer',
        'amount' => $amountFixed,
        'email' => request()->email,
        'tx_ref' => $reference,
        'currency' => $this->settings->currency_code,
        'redirect_url' => route('flutterwaveCallback'),
        'customer' => [
          'email' => auth()->user()->email,
          "name" => auth()->user()->name
        ],

        "meta" => [
          "user" => auth()->id(),
          "amountFinal" => $this->request->amount,
          "taxes" => $this->settings->tax_on_wallet ? auth()->user()->taxesPayable() : null,
          "mode" => "deposit",
          "redirect" => url('user/dashboard/add/funds')
        ],

        "customizations" => [
          "title" => __('misc.add_funds') . ' @' . auth()->user()->username
        ]
      ];

      $payment = Flutterwave::initializePayment($data);

      if ($payment['status'] !== 'success') {
        return response()->json([
          'success' => false,
          'errors' => ['error' => __('misc.error')],
        ]);
      }

      return response()->json([
        'success' => true,
        'url' => $payment['data']['link']
      ]);

    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'errors' => ['error' => $e->getMessage()],
      ]);
    }
  } // End Add funds

  // Buy
  public function buy()
  {

    if (!$this->request->expectsJson()) {
      abort(404);
    }

    try {

      $taxes = $this->settings->tax_on_wallet ? ($this->request->amount * auth()->user()->isTaxable()->sum('percentage') / 100) : 0;

      // Get Image
      $image = Images::where('token_id', $this->request->token)->firstOrFail();

      $priceItem = $this->settings->default_price_photos ?: $image->price;

      $itemPrice = $this->priceItem($this->request->license, $priceItem, $this->request->type);

      $itemPrice = applyCouponToPrice($itemPrice);

      //This generates a payment reference
      $reference = Flutterwave::generateReference();

      // Enter the details of the payment
      $data = [
        'payment_options' => 'card,banktransfer',
        'amount' => Helper::amountGross($itemPrice),
        'email' => request()->email,
        'tx_ref' => $reference,
        'currency' => $this->settings->currency_code,
        'redirect_url' => route('flutterwaveCallback'),
        'customer' => [
          'email' => auth()->user()->email,
          "name" => auth()->user()->name
        ],

        "meta" => [
          'userId' => auth()->id(),
          'imageId' => $image->id,
          'license' => $this->request->license,
          'type' => $this->request->type,
          'taxes' => auth()->user()->taxesPayable(),
          'mode' => 'sale',
          "redirect" => url('user/dashboard/purchases')
        ],

        "customizations" => [
          "title" => trans('misc.' . $this->request->type . '_photo') . ' - ' . trans('misc.license_' . $this->request->license)
        ]
      ];

      $payment = Flutterwave::initializePayment($data);

      if ($payment['status'] !== 'success') {
        return response()->json([
          'success' => false,
          'errors' => ['error' => __('misc.error')],
        ]);
      }

      return response()->json([
        'success' => true,
        'url' => $payment['data']['link']
      ]);

    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'errors' => ['error' => $e->getMessage()],
      ]);
    }

  } // End Buy

  public function callback(Request $request)
  {
    $status = request()->status;

    $paymentGateway = PaymentGateways::whereName('Flutterwave')->firstOrFail();

    //if payment is successful
    if ($status == 'successful') {

      $transactionID = Flutterwave::getTransactionIDFromCallback();
      $data = Flutterwave::verifyTransaction($transactionID);

      if ($data['data']['meta']['mode'] == 'deposit') {
        $verifyTxnId = Deposits::whereTxnId($data['data']['tx_ref'])->first();

        if (
          $data['data']['status'] == "successful"
          && $data['data']['amount'] >= $data['data']['meta']['amountFinal']
          && $data['data']['currency'] == $this->settings->currency_code
          && !$verifyTxnId
        ) {
          // Insert Deposit
          $this->deposit(
            $data['data']['meta']['user'],
            $data['data']['tx_ref'],
            $data['data']['meta']['amountFinal'],
            'Flutterwave',
            $data['data']['meta']['taxes'] ?? null
          );

          // Add Funds to User
          User::find($data['data']['meta']['user'])->increment('funds', $data['data']['meta']['amountFinal']);

        }
      } // End Deposit

      if ($data['data']['meta']['mode'] == 'sale') {

        // Check if purchase exists txn_id
        $verifyTxnId = Purchases::whereTxnId($data['data']['tx_ref'])->first();

        if (
          $data['data']['status'] == "successful"
          && $data['data']['currency'] == $this->settings->currency_code
          && !$verifyTxnId
        ) {

          // Get Image
          $image = Images::findOrFail($data['data']['meta']['imageId']);

          // Price Item
          $priceItem = $this->settings->default_price_photos ?: $image->price;

          $itemPrice = $this->priceItem($data['data']['meta']['license'], $priceItem, $data['data']['meta']['type']);

          $itemPrice = applyCouponToPrice($itemPrice);

          // Admin and user earnings calculation
          $earnings = $this->earningsAdminUser($image->user()->author_exclusive, $itemPrice, $paymentGateway->fee, $paymentGateway->fee_cents);

          // Insert purchase
          $this->purchase(
            $data['data']['tx_ref'],
            $image,
            $data['data']['meta']['userId'],
            $itemPrice,
            $earnings['user'],
            $earnings['admin'],
            $data['data']['meta']['type'],
            $data['data']['meta']['license'],
            $earnings['percentageApplied'],
            'Flutterwave',
            $data['data']['meta']['taxes'],
            false
          );
        }
      } // End Sale

    } // end payment is successful

    return redirect($data['data']['meta']['redirect'] ?? '/');

  } //<----- End Method callback()
}
