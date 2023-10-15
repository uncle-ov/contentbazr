<?php

namespace App\Http\Controllers;

use App\Models\AdminSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests;
use App\Models\User;
use App\Models\Collections;
use App\Models\UsersReported;
use App\Models\Stock;
use App\Models\Images;
use App\Models\ImagesReported;
use App\Models\Notifications;
use App\Models\Followers;
use App\Models\Downloads;
use App\Models\Like;
use App\Models\Replies;
use App\Models\Comments;
use App\Models\CollectionsImages;
use App\Models\Pages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Countries;

class AdminUserController extends Controller
{

	use Traits\UserTrait;

	protected function validator(array $data, $id = null)
	{

		Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
			return !preg_match('/[^x00-x7F\-]/i', $value);
		});

		return Validator::make($data, [
			'email' => 'required|email|max:255|unique:users,id,' . $id,
		]);

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$query = request()->get('q');

		if ($query != '' && strlen($query) > 2) {
			$data = User::where('name', 'LIKE', '%' . $query . '%')
				->orWhere('username', 'LIKE', '%' . $query . '%')
				->orderBy('id', 'desc')->paginate(20);
		} else {
			$data = User::orderBy('id', 'desc')->paginate(20);
		}

		return view('admin.members', ['data' => $data, 'query' => $query]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function new()
	{
		$data = ['admin_user_creation' => true];
		return view('admin.new-member')->withData($data);
	}

	public function createUser(Request $request)
	{
		$validation = [
			'username' => 'required|min:3|max:15|alpha_dash|unique:users|unique:pages,slug|unique:reserved,name',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|min:8|confirmed',
		];

		$settings = AdminSettings::first();
		$data = $request->all();

		$validator = Validator::make($data, $validation);

		if ($validator->fails()) {
			return back()->withErrors($validator);
		} else {
			$country = Countries::whereCountryCode(\Helper::userCountry())->first();

			return User::create([
				'username' => $data['username'],
				'name' => '',
				'bio' => '',
				'countries_id' => $country->id ?? '',
				'password' => bcrypt($data['password']),
				'email' => strtolower($data['email']),
				'avatar' => $settings->avatar,
				'cover' => $settings->cover,
				'status' => 'active',
				'type_account' => '1',
				'website' => '',
				'twitter' => '',
				'paypal_account' => '',
				'activation_code' => 'activated_by_admin',
				'oauth_uid' => '',
				'oauth_provider' => '',
				'token' => '',
				'authorized_to_upload' => 'yes',
				'ip' => request()->ip()
			]);

			return redirect('panel/admin/members')
				->withSuccessMessage(__('New user successfully created.'));
		}
	}

	public function edit($id)
	{

		$data = User::findOrFail($id);

		if ($data->id == 1 || $data->id == Auth::user()->id) {
			\Session::flash('info_message', trans('admin.user_no_edit'));
			return redirect('panel/admin/members');
		}
		return view('admin.edit-member')->withData($data);

	} //<--- End Method

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{

		$user = User::findOrFail($id);

		$input = $request->all();

		if (!$request->authorized_to_upload) {
			$input['authorized_to_upload'] = 'no';
		}

		$validator = $this->validator($input, $id);

		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		if ($request->status == 'suspended') {
			$this->userSuspended($id);
		}

		$user->fill($input)->save();

		\Session::flash('success_message', trans('admin.success_update'));

		return redirect('panel/admin/members');

	} //<--- End Method


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */

	public function destroy($id)
	{

		$user = User::findOrFail($id);

		if ($user->id == 1 || $user->id == Auth::user()->id) {
			return redirect('panel/admin/members');
			exit;
		}

		$this->deleteUser($id);

		return redirect('panel/admin/members');

	} //<--- End Method

	public function userSuspended($id)
	{

		// Collections
		$collections = Collections::where('user_id', '=', $id)->get();

		if (isset($collections)) {
			foreach ($collections as $collection) {

				// Collections
				$collectionsImages = CollectionsImages::where('images_id', '=', $collection->id)->get();
				if (isset($collectionsImages)) {
					foreach ($collectionsImages as $collectionsImage) {
						$collectionsImage->delete();
					}
				}
				$collection->delete();
			}
		}

		// Comments Delete
		$comments = Comments::where('user_id', '=', $id)->get();

		if (isset($comments)) {
			foreach ($comments as $comment) {
				$comment->delete();
			}
		}

		// Replies
		$replies = Replies::where('user_id', '=', $id)->get();

		if (isset($replies)) {
			foreach ($replies as $replie) {
				$replies->delete();
			}
		}

		// Likes
		$likes = Like::where('user_id', '=', $id)->get();
		if (isset($likes)) {
			foreach ($likes as $like) {
				$like->delete();
			}
		}

		// Downloads
		$downloads = Downloads::where('user_id', '=', $id)->get();
		if (isset($downloads)) {
			foreach ($downloads as $download) {
				$download->delete();
			}
		}

		// Followers
		$followers = Followers::where('follower', $id)->orwhere('following', $id)->get();
		if (isset($followers)) {
			foreach ($followers as $follower) {
				$follower->delete();
			}
		}

		// Delete Notification
		$notifications = Notifications::where('author', $id)
			->orWhere('destination', $id)
			->get();

		if (isset($notifications)) {
			foreach ($notifications as $notification) {
				$notification->delete();
			}
		}

		// Images Reported
		$images_reporteds = ImagesReported::where('user_id', '=', $id)->get();

		if (isset($images_reporteds)) {
			foreach ($images_reporteds as $images_reported) {
				$images_reported->delete();
			} // End
		}

		// Images
		$images = Images::where('user_id', '=', $id)->get();

		if (isset($images)) {
			foreach ($images as $image) {

				// Collections Images
				$collectionsImagesUsers = CollectionsImages::where('images_id', '=', $image->id)->get();
				if (isset($collectionsImagesUsers)) {
					foreach ($collectionsImagesUsers as $collectionsImagesUser) {
						$collectionsImagesUser->delete();
					}
				}

				//<---- ALL RESOLUTIONS IMAGES
				$stocks = Stock::where('images_id', '=', $image->id)->get();

				foreach ($stocks as $stock) {

					// Delete Stock
					Storage::delete(config('path.uploads') . $stock->type . '/' . $stock->name);

					// Delete Stock Vector
					Storage::delete(config('path.files') . $stock->name);

					$stock->delete();
				}

				// Delete preview
				Storage::delete(config('path.preview') . $image->preview);

				// Delete thumbnail
				Storage::delete(config('path.thumbnail') . $image->thumbnail);

				$image->delete();
			}
		} // End

		// User Reported
		$users_reporteds = UsersReported::where('user_id', '=', $id)->orWhere('id_reported', '=', $id)->get();

		if (isset($users_reporteds)) {
			foreach ($users_reporteds as $users_reported) {
				$users_reported->delete();
			} // End
		}

	}


}