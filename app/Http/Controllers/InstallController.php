<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentGateways;
use App\Models\User;
use App\Models\AdminSettings;

class InstallController extends Controller
{
    public function __construct() {
      $this->middleware('role');
    }

    public function install($addon)
    {
      //<-------------- Install --------------->
      if ($addon == 'flutterwave') {

        $verifyPayment = PaymentGateways::whereName('Flutterwave')->first();

        if ($verifyPayment) {
          $verifyPayment->delete();
        }

        if (! $verifyPayment) {

          // Controller
          $filePathController = 'flutterwave-payment/FlutterwaveController.php';
          $pathController = app_path('Http/Controllers/FlutterwaveController.php');

          if (\File::exists($filePathController) ) {
            rename($filePathController, $pathController);
          }//<--- IF FILE EXISTS

          // View
          $filePathView = 'flutterwave-payment/flutterwave-settings.blade.php';
          $pathView = resource_path('views/admin/flutterwave-settings.blade.php');

          if (\File::exists($filePathView) ) {
            rename($filePathView, $pathView);
          }//<--- IF FILE EXISTS

          // Image
          $filePathImage = 'flutterwave-payment/flutterwave.png';
          $pathImage = public_path('img/payments/flutterwave.png');

          if (\File::exists($filePathImage) ) {
            rename($filePathImage, $pathImage);
          }//<--- IF FILE EXISTS

          file_put_contents(
              'routes/web.php',
              "\nRoute::get('payment/flutterwave', 'FlutterwaveController@show')->name('flutterwave');\nRoute::get('payment/flutterwave/buy', 'FlutterwaveController@buy')->name('flutterwave.buy');\nRoute::get('callback/flutterwave', 'FlutterwaveController@callback')->name('flutterwaveCallback');\n",
              FILE_APPEND
          );

          if (Schema::hasTable('payment_gateways')) {
              \DB::table('payment_gateways')->insert(
      				[
                'name' => 'Flutterwave',
      					'type' => 'card',
      					'enabled' => '0',
      					'fee' => 0.0,
      					'fee_cents' => 0.00,
      					'email' => '',
      					'key' => '',
      					'key_secret' => '',
      					'logo' => 'flutterwave.png',
      					'token' => str_random(150),
      			]
          );
        }

        $indexPath = 'flutterwave-payment/index.php';
        unlink($indexPath);

        rmdir('flutterwave-payment');

        $getPayment = PaymentGateways::whereName('Flutterwave')->firstOrFail();

          return redirect('panel/admin/payments/'.$getPayment->id);
        } else {
          return redirect('/');
        }

    }
  }//<---------------------- End Install

}
