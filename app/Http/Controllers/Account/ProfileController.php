<?php

namespace App\Http\Controllers\Account;

use App\Http\Cache\CacheImage;
use App\Http\Cache\CacheUser;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        $params = [
            'user' => $user,
        ];

        return Inertia::render('Profile/Profile', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  User  $user
     * @return Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:64'],
            'mobile' => ['nullable', 'string', 'max:13', 'min:11'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        $user = $request->user();

        $user->update([
			'name' => $request->name,
			'mobile' => $request->mobile,
			'address' => $request->address,
		]);

        DB::commit();
        CacheUser::forget();

        return back()->with('success', 'Profile updated.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function updateImage(Request $request)
    {
        $request->validate([
			'image_file'	=> ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'image_removed' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

		if ($request->image_removed) {
			$user->destroyImage();
			$user->update([ 'image' => "" ]);
			return back()->with('success', 'Profile Image removed!');
		}

		if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
			$image_path = $user->storeImage($request->file('image_file'), 'images/users');
			$user->update([ 'image' => $image_path ]);
			return back()->with('success', 'Profile Image uploaded!');
		}

        return back()->with('fail', 'Profile Image changing failed!');
	}

    /**
     * Show the application profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function password(Request $request)
    {
        $profile = $request->user();
        //die($profile);

        $params = [
            'profile' => $profile,
        ];

        return Inertia::render('Profile/Password', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  User  $user
     * @return Response
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();
		
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::beginTransaction();
        $user->password = Hash::make($request->new_password);
        $user->update();

        DB::commit();
        CacheUser::forget();

        return back()->with('success', __('Password modified successfully!'));
    }
}
