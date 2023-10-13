<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Models\User;
use App\Models\Images;
use App\Models\Followers;
use App\Models\Like;
use App\Models\ImagesReported;
use App\Models\Stock;
use App\Models\AdminSettings;
use App\Models\Downloads;
use App\Models\Notifications;
use App\Models\Visits;
use App\Models\Collections;
use App\Models\CollectionsImages;
use App\Models\PaymentGateways;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use Image;
use App\Models\Purchases;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use Illuminate\Support\Str;

class ImagesController extends Controller
{

	use Traits\UploadTrait, Traits\FunctionsTrait;

	public function __construct(AdminSettings $settings, Request $request)
	{
		$this->settings = $settings::first();
		$this->request = $request;
	}

	protected function validatorUpdate(array $data)
	{
		Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
			return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		$sizeAllowed = $this->settings->file_size_allowed * 1024;

		$dimensions = explode('x', $this->settings->min_width_height_image);

		if ($this->settings->currency_position == 'right') {
			$currencyPosition = 2;
		} else {
			$currencyPosition = null;
		}

		$messages = array(
			'photo.required' => trans('misc.please_select_image'),
			"photo.max" => trans('misc.max_size') . ' ' . Helper::formatBytes($sizeAllowed, 1),
			"price.required_if" => trans('misc.price_required'),
			'price.min' => trans('misc.price_minimum_sale' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
			'price.max' => trans('misc.price_maximum_sale' . $currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),

		);

		// Create Rules
		return Validator::make($data, [
			'title' => 'required|min:3|max:' . $this->settings->title_length . '',
			'description' => 'min:2|max:' . $this->settings->description_length . '',
			'tags' => 'required',
			'price' => 'required_if:item_for_sale,==,sale|integer|min:' . $this->settings->min_sale_amount . '|max:' . $this->settings->max_sale_amount . ''
		], $messages);

	}

	/**
	 * Upload Section
	 *
	 * @return View
	 */
	public function showUpload()
	{
		if (auth()->user()->authorized_to_upload == 'yes' || auth()->user()->isSuperAdmin()) {
			return view('images.upload');
		} else {
			return redirect('/');
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = Images::all();

		return view('admin.images')->withData($data);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $slug = null)
	{

		\Artisan::call('dump-autoload');
		dd('dump-autoload complete');

		$response = Images::findOrFail($id);

		if (auth()->check() && $response->user_id != auth()->id() && $response->status == 'pending' && auth()->user()->role != 'admin') {
			abort(404);
		} else if (auth()->guest() && $response->status == 'pending') {
			abort(404);
		}

		$uri = $this->request->path();

		if (str_slug($response->title) == '') {

			$slugUrl = '';
		} else {
			$slugUrl = '/' . str_slug($response->title);
		}

		$url_image = 'template/' . $response->id . $slugUrl;

		//<<<-- * Redirect the user real page * -->>>
		$uriImage = $this->request->path();
		$uriCanonical = $url_image;

		if ($uriImage != $uriCanonical) {
			return redirect($uriCanonical);
		}

		//<--------- * Visits * ---------->
		$user_IP = request()->ip();
		$date = time();

		if (auth()->check()) {
			// SELECT IF YOU REGISTERED AND VISITED THE PUBLICATION
			$visitCheckUser = $response->visits()->where('user_id', auth()->id())->first();

			if (!$visitCheckUser && auth()->id() != $response->user()->id) {
				$visit = new Visits;
				$visit->images_id = $response->id;
				$visit->user_id = auth()->id();
				$visit->ip = $user_IP;
				$visit->save();
			}

		} else {

			// IF YOU SELECT "UNREGISTERED" ALREADY VISITED THE PUBLICATION
			$visitCheckGuest = $response->visits()->where('user_id', 0)
				->where('ip', $user_IP)
				->orderBy('date', 'desc')
				->first();

			if ($visitCheckGuest) {
				$dateGuest = strtotime($visitCheckGuest->date) + (7200); // 2 Hours
			}

			if (empty($visitCheckGuest->ip)) {
				$visit = new Visits();
				$visit->images_id = $response->id;
				$visit->user_id = 0;
				$visit->ip = $user_IP;
				$visit->save();
			} else if ($dateGuest < $date) {
				$visit = new Visits();
				$visit->images_id = $response->id;
				$visit->user_id = 0;
				$visit->ip = $user_IP;
				$visit->save();
			}

		} //<--------- * Visits * ---------->

		if (auth()->check()) {

			// FOLLOW ACTIVE
			$followActive = Followers::where('follower', auth()->id())
				->where('following', $response->user()->id)
				->where('status', '1')
				->first();

			if ($followActive) {
				$textFollow = trans('users.following');
				$icoFollow = '-person-check';
				$activeFollow = 'btnFollowActive';
			} else {
				$textFollow = trans('users.follow');
				$icoFollow = '-person-plus';
				$activeFollow = '';
			}

			// LIKE ACTIVE
			$likeActive = Like::where('user_id', auth()->id())
				->where('images_id', $response->id)
				->where('status', '1')
				->first();

			if ($likeActive) {
				$textLike = trans('misc.unlike');
				$icoLike = 'bi bi-heart-fill';
				$statusLike = 'active';
			} else {
				$textLike = trans('misc.like');
				$icoLike = 'bi bi-heart';
				$statusLike = '';
			}

			// ADD TO COLLECTION
			$collections = Collections::where('user_id', auth()->id())->orderBy('id', 'asc')->get();

		} //<<<<---- *** END AUTH ***

		// All Images resolutions
		$stockImages = $response->stock;

		$resolution = []; //explode('x', Helper::resolutionPreview($stockImages[1]->resolution));
		$previewWidth = 0; //$resolution[0];
		$previewHeight = 0; //$resolution[1];

		// Similar Photos
		$arrayTags = explode(",", $response->tags);
		$countTags = count($arrayTags);

		$images = Images::where('categories_id', $response->categories_id)
			->whereStatus('active')
			->where(function ($query) use ($arrayTags, $countTags) {
				for ($k = 0; $k < $countTags; ++$k) {
					$query->orWhere('tags', 'LIKE', '%' . $arrayTags[$k] . '%');
				}
			})
			->where('id', '<>', $response->id)
			->orderByRaw('RAND()')
			->take(20)
			->get();

		// Comments
		$comments_sql = $response->comments()->where('status', '1')->orderBy('date', 'desc')->paginate(10);

		// Payments gateways enabled
		$paymentsGatewaysEnabled = PaymentGateways::where('enabled', '1')->count();

		// Item price
		$itemPrice = $this->settings->default_price_photos ?: $response->price;


		return view('images.show')->with([
			'response' => $response,
			'textFollow' => $textFollow ?? null,
			'icoFollow' => $icoFollow ?? null,
			'activeFollow' => $activeFollow ?? null,
			'textLike' => $textLike ?? null,
			'icoLike' => $icoLike ?? null,
			'statusLike' => $statusLike ?? null,
			'collections' => $collections ?? null,
			'stockImages' => $stockImages,
			'previewWidth' => $previewWidth,
			'previewHeight' => $previewHeight,
			'images' => $images,
			'comments_sql' => $comments_sql,
			'paymentsGatewaysEnabled' => $paymentsGatewaysEnabled,
			'itemPrice' => $itemPrice,
			'token' => $response->token_id,
			'stock_size' => $stockImages[0]->size,
		]);

	} //<--- End Method

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$data = Images::findOrFail($id);

		if ($data->user_id != auth()->id()) {
			abort('404');
		}

		return view('images.edit')->withData($data);

	} //<--- End Method

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request)
	{
		$image = Images::findOrFail($request->id);

		if ($image->user_id != auth()->id()) {
			return redirect('/');
		}

		$input = $request->all();

		$input['tags'] = Helper::cleanStr($input['tags']);

		if (strlen($input['tags']) == 1) {
			return redirect()->back()
				->withErrors(trans('validation.required', ['attribute' => trans('misc.tags')]));
		}

		$tagsLength = explode(',', $input['tags']);

		// Validate length tags
		foreach ($tagsLength as $tag) {
			if (strlen($tag) < 2) {
				return redirect()->back()
					->withErrors(trans('misc.error_length_tags'));
			}
		}

		// Validate number of tags
		if (count($tagsLength) > $this->settings->tags_limit && !$image->data_iptc) {
			return redirect()->back()
				->withErrors(trans('misc.maximum_tags', ['limit' => $this->settings->tags_limit]));
		}

		if ($image->item_for_sale == 'sale' || $request->item_for_sale == 'sale') {
			$input['item_for_sale'] = 'sale';
		} else {
			$input['item_for_sale'] = 'free';
		}

		$input['attribution_required'] = $request->attribution_required ?? 'no';

		$validator = $this->validatorUpdate($input);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		if ($this->settings->default_price_photos) {
			$input['price'] = $image->price;
		}

		$image->fill($input)->save();

		\Session::flash('success_message', trans('admin.success_update'));

		return redirect('edit/template/' . $image->id);

	} //<--- End Method


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{
		$image = Images::findOrFail($request->id);

		if ($image->user_id != auth()->id()) {
			return redirect('/');
		}

		// Delete Notification
		$notifications = Notifications::where('destination', $request->id)
			->where('type', '2')
			->orWhere('destination', $request->id)
			->where('type', '3')
			->orWhere('destination', $request->id)
			->where('type', '4')
			->get();

		if (isset($notifications)) {
			foreach ($notifications as $notification) {
				$notification->delete();
			}
		}

		// Collections Images
		$collectionsImages = CollectionsImages::where('images_id', '=', $request->id)->get();
		if (isset($collectionsImages)) {
			foreach ($collectionsImages as $collectionsImage) {
				$collectionsImage->delete();
			}
		}

		// Images Reported
		$imagesReporteds = ImagesReported::where('image_id', '=', $request->id)->get();
		if (isset($imagesReporteds)) {
			foreach ($imagesReporteds as $imagesReported) {
				$imagesReported->delete();
			}
		}

		//<---- ALL RESOLUTIONS IMAGES
		$stocks = Stock::where('images_id', '=', $request->id)->get();

		foreach ($stocks as $stock) {
			// Delete Stock
			Storage::delete(config('path.uploads') . $stock->type . '/' . $stock->name);

			// Delete Stock Vector
			Storage::delete(config('path.files') . $stock->name);

			$stock->delete();

		} //<--- End foreach

		// Delete preview
		Storage::delete(config('path.preview') . $image->preview);

		// Delete thumbnail
		Storage::delete(config('path.thumbnail') . $image->thumbnail);

		$image->delete();

		return redirect(auth()->user()->username);

	} //<--- End Method

	public function download($token_id)
	{
		$type = $this->request->type;
		if (empty($type))
			$type = 'small';

		$image = Images::where('token_id', $token_id)->where('item_for_sale', 'free')->whereRaw('download <> ""')->firstOrFail();

		// Get stock image
		$getImage = Stock::where('images_id', $image->id)->firstOrFail();


		// Download Check User
		$user_IP = request()->ip();
		$date = time();

		if (auth()->check()) {

			$downloadCheckUser = $image->downloads()->whereUserId(auth()->id())->whereSize($type)->first();
			$dailyDownloads = auth()->user()->freeDailyDownloads();

			if (
				!$downloadCheckUser
				&& $this->settings->daily_limit_downloads != 0
				&& $dailyDownloads == $this->settings->daily_limit_downloads
				&& auth()->id() != $image->user()->id
			) {
				return back()->withError(trans('misc.reached_daily_download'));
			}

			if (!$downloadCheckUser && auth()->id() != $image->user()->id) {
				$download = new Downloads();
				$download->images_id = $image->id;
				$download->user_id = auth()->id();
				$download->ip = $user_IP;
				$download->type = 'free';
				$download->size = $type;
				$download->save();
			}
		} // Auth check
		else {

			// IF YOU SELECT "UNREGISTERED" ALREADY DOWNLOAD THE IMAGE
			$downloadCheckUser = $image->downloads()->where('user_id', 0)
				->where('ip', $user_IP)
				->orderBy('date', 'desc')
				->first();

			if ($downloadCheckUser) {
				$dateGuest = strtotime($downloadCheckUser->date) + (7200); // 2 Hours
			}

			if (empty($downloadCheckUser->ip)) {
				$download = new Downloads;
				$download->images_id = $image->id;
				$download->user_id = 0;
				$download->ip = $user_IP;
				$download->save();
			} else if ($dateGuest < $date) {
				$download = new Downloads;
				$download->images_id = $image->id;
				$download->user_id = 0;
				$download->ip = $user_IP;
				$download->save();
			}

		} //<--------- * Visits * ---------->
		//<<<<---/ Download Check User

		$headers = [
			'Content-Type:' => $image->mime_type,
			'Cache-Control' => 'no-cache, no-store, must-revalidate',
			'Pragma' => 'no-cache',
			'Expires' => '0'
		];

		$file_path = '/uploads/files/' . $image->download . '.zip';
		$file_title = Str::slug($image->title) . '.zip';

		return Storage::disk('azure')->download($file_path, $file_title, $headers);

		// return response()->download($file_path, $file_title);
	} //<--- End Method

	public function report(Request $request)
	{

		$data = ImagesReported::firstOrNew(['user_id' => auth()->id(), 'image_id' => $request->id]);

		if ($data->exists) {
			\Session::flash('noty_error', 'error');
			return redirect()->back();
		} else {

			$data->reason = $request->reason;
			$data->save();
			\Session::flash('noty_success', 'success');
			return redirect()->back();
		}

	} //<--- End Method

	public function purchase($token_id)
	{
		$license = strtolower($this->request->license);
		$urlDashboardUser = url('user/dashboard/purchases');

		if (url()->previous() == $urlDashboardUser && !$this->request->downloadAgain) {
			abort(404);
		}

		$image = Images::where('token_id', $token_id)->whereRaw('download <> ""')->firstOrFail();

		// Validate Licenses and Type
		$licensesArray = ['regular', 'extended'];
		$typeArray = ['small', 'medium', 'large', 'vector'];

		// License
		if (!in_array($license, $licensesArray) && auth()->id() != $image->user()->id) {
			abort(404);
		}

		$getImage = Stock::where('images_id', $image->id)->firstOrFail();

		// Download image from the user's Dashboard
		if ($this->request->downloadAgain) {
			return $this->downloadAgain($image, $getImage);
		}

		$headers = [
			'Content-Type:' => $image->mime_type,
			'Cache-Control' => 'no-cache, no-store, must-revalidate',
			'Pragma' => 'no-cache',
			'Expires' => '0'
		];

		$file_path = '/uploads/files/' . $image->download . '.zip';
		$file_title = Str::slug($image->title) . '.zip';

		return Storage::disk('azure')->download($file_path, $file_title, $headers);
		// return response()->download($file_path, $file_title);

	} //<--- End Method

	public function subscriptionDownload($token_id)
	{
		$type = strtolower($this->request->type);
		$license = strtolower($this->request->license);
		$urlDashboardUser = url('user/dashboard/downloads');

		if (url()->previous() == $urlDashboardUser && !$this->request->downloadAgain) {
			abort(404);
		}

		$image = Images::where('token_id', $token_id)->whereRaw('download <> ""')->firstOrFail();

		// Validate Type
		$typeArray = ['small', 'medium', 'large', 'vector'];

		// Type
		if (!in_array($type, $typeArray) && auth()->id() != $image->user()->id) {
			abort(404);
		}

		$getImage = Stock::where('images_id', $image->id)->where('type', '=', $type)->firstOrFail();

		$downloadCheckUser = $image->downloads()->whereUserId(auth()->id())->whereType('subscription')->whereSize($type)->first();
		$dailyDownloads = auth()->user()->subscriptionDailyDownloads();

		if (!auth()->user()->getSubscription() && !$downloadCheckUser) {
			return back()->withError(__('misc.not_subscribed'));
		}

		if (!$downloadCheckUser) {

			$planUser = auth()->user()->getSubscription();
			$itemPrice = $planUser->interval == 'month'
				? Helper::calculatePriceGrossByDownloads($planUser->plan->price, $planUser->plan->downloads_per_month, true)
				: Helper::calculatePriceGrossByDownloads($planUser->plan->price_year, $planUser->plan->downloads_per_month);

			if ($planUser->plan->download_limits != 0 && $dailyDownloads >= $planUser->plan->download_limits) {
				return back()->withError(__('misc.reached_daily_download'));
			}

			if (auth()->user()->downloads == 0) {
				return back()->withError(__('misc.reached_download_limit_plan'));
			}

			// Admin and user earnings calculation
			$earnings = $this->earningsAdminUser($image->user()->author_exclusive, $itemPrice, null, null);
			$directPayment = false;

			// Stripe Connect
			if ($image->user()->stripe_connect_id && $image->user()->completed_stripe_onboarding && $planUser->payment_gateway == 'Stripe') {
				try {
					$payment = PaymentGateways::whereName('Stripe')->whereEnabled(1)->first();
					// Stripe Client
					$stripe = new \Stripe\StripeClient($payment->key_secret);

					$earningsUser = $this->settings->currency_code == 'JPY' ? $earnings['user'] : ($earnings['user'] * 100);

					$stripe->transfers->create([
						'amount' => $earningsUser,
						'currency' => $this->settings->currency_code,
						'destination' => $image->user()->stripe_connect_id,
						'description' => trans('misc.stock_photo_purchase')
					]);

					$directPayment = true;

				} catch (\Exception $e) {
					\Log::info($e->getMessage());
				}
			}

			// Referred
			$earningAdminReferred = $this->referred(auth()->id(), $earnings['admin'], 'photo');

			// Insert Purchase
			$purchase = new Purchases();
			$purchase->txn_id = 'psub_' . str_random(25);
			$purchase->images_id = $image->id;
			$purchase->user_id = auth()->id();
			$purchase->price = $itemPrice;
			$purchase->earning_net_seller = $earnings['user'];
			$purchase->earning_net_admin = $earningAdminReferred ?: $earnings['admin'];
			$purchase->payment_gateway = $planUser->payment_gateway;
			$purchase->type = $type;
			$purchase->license = $license;
			$purchase->order_id = substr(strtolower(md5(microtime() . mt_rand(1000, 9999))), 0, 15);
			$purchase->purchase_code = implode('-', str_split(substr(strtolower(md5(time() . mt_rand(1000, 9999))), 0, 27), 5));
			$purchase->mode = 'subscription';
			$purchase->percentage_applied = $earnings['percentageApplied'];
			$purchase->referred_commission = $earningAdminReferred ? true : false;
			$purchase->direct_payment = $directPayment;
			$purchase->save();

			// Insert Download
			$download = new Downloads();
			$download->images_id = $image->id;
			$download->user_id = auth()->id();
			$download->ip = request()->ip();
			$download->type = 'subscription';
			$download->size = $type;
			$download->save();

			// Add Balance And Notify to User
			$amountUserEarning = $directPayment ? 0 : $earnings['user'];

			$this->AddBalanceAndNotify($image, auth()->id(), $amountUserEarning);

			// Subtract download to user
			auth()->user()->decrement('downloads', 1);
		}

		$headers = [
			'Content-Type:' => $image->mime_type,
			'Cache-Control' => 'no-cache, no-store, must-revalidate',
			'Pragma' => 'no-cache',
			'Expires' => '0'
		];

		$file_path = '/uploads/files/' . $image->download . '.zip';
		$file_title = Str::slug($image->title) . '.zip';

		return Storage::disk('azure')->download($file_path, $file_title, $headers);

		// return response()->download($file_path, $file_title);
	} //<--- End Method

	protected function downloadAgain($image, $getImage)
	{

		$verifyPurchaseUserAgain = $image->purchases()
			->where('user_id', auth()->id())
			->where('images_id', $image->id)
			->where('type', '=', $this->request->type)
			->where('license', '=', $this->request->license)
			->first();

		if (!$verifyPurchaseUserAgain) {
			abort(404);
		}

		if ($this->request->type != 'vector') {
			$pathFile = config('path.uploads') . $this->request->type . '/' . $getImage->name;
			$resolution = $getImage->resolution;
		} else {
			$pathFile = config('path.files') . $getImage->name;
			$resolution = trans('misc.vector_graphic');
		}

		$headers = [
			'Content-Type:' => ' image/' . $image->extension,
			'Cache-Control' => 'no-cache, no-store, must-revalidate',
			'Pragma' => 'no-cache',
			'Expires' => '0'
		];

		return Storage::download($pathFile, $image->title . ' - ' . $resolution . '.' . $getImage->extension, $headers);

	} //<--- End Method

	public function uploadFiles()
	{
		$input = $this->request->all();
		// dd($input);
		$uploaded_files = [];
		$token_id = str_random(200);

		if (!$this->request->hasfile('files')) {
			$validation_error = 'files';
		} elseif (!$this->request->hasfile('images')) {
			$validation_error = 'images';
		}

		if (!empty($validation_error)) {
			return redirect()->back()
				->withErrors(trans('validation.required', ['attribute' => $validation_error]));

		}

		if ($this->settings->auto_approve_images == 'on') {
			$status = 'active';
		} else {
			$status = 'pending';
		}

		if (!$this->request->price) {
			$price = 0;
		} else {
			$price = $input['price'];
		}

		if (!empty($this->request->description)) {
			$description = Helper::checkTextDb($this->request->description);
		} else {
			$description = '';
		}


		foreach (['images', 'files', 'videos'] as $k => $field) {
			if (!empty($input[$field]) && $this->request->hasfile($field)) {
				$input_field = $input[$field];

				foreach ($input_field as $key => $file) {
					$original_name = $file->getClientOriginalName();
					$ext = $file->getClientOriginalExtension();

					$name = str_random(20) . '.' . $ext;
					$dir = '/uploads/' . $field . '/';
					$file_path = public_path() . $dir;

					if ($field == 'files') {
						$file->move($file_path, $name);
					} else {
						$filePath = $file->storeAs($dir, $name, 'azure');
					}

					$resolution = '';
					$width = '';
					$height = '';
					$colors = '';

					if ($field == 'images') {
						list($width, $height) = getimagesize($file->getPathName());
						$resolution = $width . 'x' . $height;

						if ($key === 0) {
							$url = Storage::disk('azure')->url($dir . $name);
							$colors = $this->getColorsFromImageUrl($url);
						}
					}

					$uploaded_files[$field][] = [
						'name' => $name,
						'mime_type' => $file->getClientMimeType(),
						'original_name' => $original_name,
						'file' => $field . '/' . $name,
						'extension' => $ext,
						'size' => Helper::formatBytes($file->getSize(), 1),
						'resolution_str' => $resolution,
						'colors' => $colors,
						'resolution' => [
							'width' => $width,
							'height' => $height,
						],
					];
				}
			}
		}

		$thumbnail = !empty($uploaded_files['images'][0]) ? $uploaded_files['images'][0]['file'] : '';
		$thumbnails = !empty($uploaded_files['images'][0]) ? json_encode($uploaded_files['images']) : '';
		$videos = !empty($uploaded_files['videos'][0]) ? json_encode($uploaded_files['videos']) : '';
		$extension = !empty($uploaded_files['files'][0]) ? $uploaded_files['files'][0]['extension'] : '';
		$original_name = !empty($uploaded_files['files'][0]) ? $uploaded_files['files'][0]['original_name'] : '';

		$download = str_random(20);

		// Zip and save files
		$dir = '/uploads/files/';
		$zip_path = public_path() . $dir;
		$filename = $download . '.zip';
		$zip = new \ZipArchive();
		$filepath = $zip_path . $filename;

		if ($zip->open($filepath, \ZipArchive::CREATE) == TRUE) {
			foreach ($uploaded_files['files'] as $file) {
				$relativeName = $file['original_name'];
				$this_file_path = public_path() . '/uploads/' . $file['file'];
				$zip->addFile($this_file_path, $relativeName);
			}
			$zip->close();
		}

		Storage::disk('azure')->put($dir . $filename, Storage::get($dir . $filename));
		unlink($filepath);

		$new_file = [
			'thumbnail' => $thumbnails,
			'preview' => $thumbnail,
			'video' => $videos,
			'vimeo_link' => $input['vimeo_link'],
			'video_dimension' => $input['video_dimension'],
			'title' => trim($input['title']),
			'description' => trim($description),
			'categories_id' => $this->request->categories_id,
			'user_id' => auth()->id(),
			'status' => $status,
			'token_id' => $token_id,
			'tags' => $input['tags'],
			'extension' => strtolower($extension),
			'colors' => $uploaded_files['images'][0]['colors'],
			'exif' => '',
			'camera' => '',
			'license' => $input['license'],
			'use_case' => $input['use_case'],
			'how_use_image' => $this->request->how_use_image,
			'attribution_required' => $this->request->attribution_required ?? 'no',
			'original_name' => $original_name,
			'download' => $download,
			'thumbnail_resolution' => $uploaded_files['images'][0]['resolution_str'],
			'price' => $this->settings->default_price_photos ?: $price,
			'item_for_sale' => $this->request->item_for_sale ? $this->request->item_for_sale : 'free',
			'vector' => 'yes',
			'data_iptc' => false,
		];

		// dd($input, $uploaded_files, $new_file);
		$image_id = Images::insertGetId($new_file);

		foreach ($uploaded_files['files'] as $file) {
			$stock = new Stock;
			$stock->images_id = $image_id;
			$stock->name = $file['original_name'];
			$stock->type = 'vector';
			$stock->mime_type = $file['mime_type'];
			$stock->extension = $file['extension'];
			$stock->resolution = '';
			$stock->size = $file['size'];
			$stock->token = $token_id;
			$stock->save();

			unlink(public_path() . '/uploads/' . $file['file']);
		}

		return redirect('edit/template/' . $image_id);
	}

	public function create()
	{
		if (auth()->guest()) {
			return response()->json([
				'session_null' => true,
				'success' => false,
			]);
		}

		return $this->upload('normal');

	}

	public function image($size, $path)
	{
		try {

			$server = ServerFactory::create([
				'response' => new LaravelResponseFactory(app('request')),
				'source' => Storage::disk()->getDriver(),
				'watermarks' => public_path('img'),
				'cache' => Storage::disk()->getDriver(),
				'source_path_prefix' => '/uploads/medium/',
				'cache_path_prefix' => '.cache',
				'base_url' => '/uploads/medium/',
			]);

			if (request()->get('size') && request()->get('size') == 'small') {
				$thumbnail = true;
			} else {
				$thumbnail = false;
			}

			if (request()->get('size') && request()->get('size') == 'medium') {
				$medium = true;
			} else {
				$medium = false;
			}

			$resolution = explode('x', Helper::resolutionPreview($size, $thumbnail, $medium));

			$width = $resolution[0];
			$height = $resolution[1];

			$server->outputImage(
				$path,
				[
					'w' => $width,
					'h' => $height,
					'mark' => $this->settings->show_watermark ? $this->settings->watermark : null,
					'markpos' => 'center',
					'markw' => '90w',
					''
				]
			);

			$server->deleteCache($path);

		} catch (\Exception $e) {

			abort(404);
			$server->deleteCache($path);
		}
	}


	public function renderAzureImage($size, $path)
	{
		try {
			$server = ServerFactory::create([
				'response' => new LaravelResponseFactory(app('request')),
				'source' => Storage::disk('azure')->getDriver(),
				'watermarks' => public_path('img'),
				'cache' => Storage::disk()->getDriver(),
				'source_path_prefix' => '/uploads/images/',
				'cache_path_prefix' => '.cache',
				'base_url' => '/uploads/images/',
			]);

			if (request()->get('size') && request()->get('size') == 'small') {
				$thumbnail = true;
			} else {
				$thumbnail = false;
			}

			if (request()->get('size') && request()->get('size') == 'medium') {
				$medium = true;
			} else {
				$medium = false;
			}

			$resolution = explode('x', Helper::resolutionPreview($size, $thumbnail, $medium));

			$width = $resolution[0];
			$height = $resolution[1];

			$server->outputImage(
				$path,
				[
					'w' => $width,
					'h' => $height,
					'mark' => $this->settings->show_watermark ? $this->settings->watermark : null,
					'markpos' => 'center',
					'markw' => '90w',
					''
				]
			);

			$server->deleteCache($path);

		} catch (\Exception $e) {

			abort(404);
			$server->deleteCache($path);
		}
	}

	public function preview($path)
	{
		$image = Stock::whereToken($path)->whereType('small')->select('name', 'resolution', 'extension')->firstOrFail();
		$resolution = $image->resolution;
		$resolution = explode('x', $image->resolution);
		$width = $resolution[0];
		$height = $resolution[1];

		$imageUrl = Storage::url(config('path.small') . $image->name);

		header('Content-type: image/' . $image->extension);
		header('Cache-Control: public, max-age=10800');
		header("Expires: " . date('D, d F Y H:i:s', strtotime('+1 year')) . ""); // Fecha en el pasado

		// Crop Image
		if (request()->get('fit') == 'crop') {

			$size_x = 400;

			if ($width > $height) {
				$new_height = $size_x;
				$new_width = ($width / $height) * $new_height;

				$x = ($width - $height) / 2;
				$y = 0;
			} else {
				$new_width = $size_x;
				$new_height = ($height / $width) * $new_width;

				$y = ($height - $width) / 2;
				$x = 0;
			}

			$newImage = imagecreatetruecolor($size_x, $size_x);
		} else {

			switch (request()->get('w')) {
				case "tiny":
					$size_x = 100;
					break;
				case "small":
					$size_x = 280;
					break;
				case "medium":
					$size_x = 480;
					break;
				default:
					$size_x = 580;
			}

			$size_y = 800;

			$resize_x = $size_x / $width;
			$resize_y = $size_y / $height;

			if ($resize_x < $resize_y) {
				$resize = $resize_x;
			} else {
				$resize = $resize_y;
			}

			$newImage = imagecreatetruecolor(ceil($width * $resize), ceil($height * $resize));
		}

		switch ($image->extension) {
			case "gif":
				$source = imagecreatefromgif($imageUrl);
				imagefill($newImage, 0, 0, imagecolorallocate($newImage, 255, 255, 255));
				imagealphablending($newImage, TRUE);
				break;
			case "pjpeg":
			case "jpeg":
			case "jpg":
				$source = imagecreatefromjpeg($imageUrl);
				break;
			case "png":
			case "x-png":
				$source = imagecreatefrompng($imageUrl);
				imagealphablending($newImage, false);
				imagesavealpha($newImage, true);
				break;
		}

		if (request()->get('fit') == 'crop') {
			imagecopyresampled($newImage, $source, 0, 0, $x, $y, $new_width, $new_height, $width, $height);
		} else {
			imagecopyresampled($newImage, $source, 0, 0, 0, 0, ceil($width * $resize), ceil($height * $resize), $width, $height);
		}

		switch ($image->extension) {
			case "gif":
				imagegif($newImage);
				break;
			case "pjpeg":
			case "jpeg":
			case "jpg":
				imagejpeg($newImage, NULL, 90);
				break;
			case "png":
			case "x-png":
				imagepng($newImage);
				break;
		}

		imagedestroy($newImage);
	}

}