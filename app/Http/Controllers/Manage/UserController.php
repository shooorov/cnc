<?php

namespace App\Http\Controllers\Manage;

use App\Http\Cache\CacheBranchAccess;
use App\Http\Cache\CacheEmailDigest;
use App\Http\Cache\CacheRole;
use App\Http\Cache\CacheUser;
use App\Http\Controllers\Controller;
use App\Models\BranchAccess;
use App\Models\EmailDigest;
use App\Models\Status;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\UseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Str;

class UserController extends Controller
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
     * Show the User list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
		$branch = $request->branch ?? '';
		$role = $request->role ?? '';
		$status = $request->status ?? 'active';
		$user = new User;

        $roles = CacheRole::get()->pluck('name', 'name')->toArray();
		$roles = array_change_key_case($roles, CASE_LOWER);
		$roles = array_merge($roles, $user->types);
		$roles = collect($roles)->map(function($value, $index) {
			return [
				"id" => $index,
				"name" => $value,
			];
		})->values();

		$available_branches = UseBranch::available()->pluck('name', 'name')->toArray();
		$available_branches = array_change_key_case($available_branches, CASE_LOWER);
		$available_branches = collect($available_branches)->map(function($value, $index) {
			return [
				"id" => $index,
				"name" => $value,
			];
		})->values();

		$statuses = collect($user->statuses)->map(function ($status, $index) {
            return [
                'id' => $index,
                'name' => $status,
            ];
        })->values()->toArray();

        $users = User::when($status, function($query, $status) {
				$query->where('status', $status);
			})->orderBy('name')->get();

		if(!empty($role)) {
			$users = $users->filter(fn ($u) => ($u->role_name == $role));
		}

		if(!empty($branch)) {
			$users = $users->filter(function ($u) use ($branch) {
				return str_contains(strtolower($u->branch_name), $branch);
			});
		}

		$params = [
            'users' => $users->values(),
            'roles' => $roles,
            'available_branches' => $available_branches,
            'statuses' => $statuses,
            'filter' => [
                'branch' => $branch,
                'role' => $role,
                'status' => $status,
            ],
        ];

        return Inertia::render('Manage/User/Index', $params);
    }

    /**
     * Show the User create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $params = [
            'user' => new User,
            'roles' => CacheRole::get(),
        ];

        return Inertia::render('Manage/User/Create', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @param  User  $user
     * @return Response
     */
    public function store(Request $request)
    {
        $user_type = $request->type;
        $is_other = $user_type == 'other';
        $is_barista = $user_type == 'barista';
        $is_chef = $user_type == 'chef';
        $is_waiter = $user_type == 'waiter';
        $is_rider = $user_type == 'rider';

        $request->validate([
            'type' => ['required', 'string'],
            'role_id' => [$is_other ? 'required' : 'nullable', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'mobile' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string', 'max:500'],
            'branches' => ['required', 'array'],
        ]);

        DB::beginTransaction();
        $user = new User;
        $user->role_id = $is_other ? $request->role_id : null;
        $user->is_barista = $is_barista;
        $user->is_chef = $is_chef;
        $user->is_waiter = $is_waiter;
        $user->is_rider = $is_rider;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->address = $request->address;
        $user->status = $user->default_status;
        $user->password = bcrypt($request->password);
        $user->save();

        foreach ($request->branches as $branch) {
            BranchAccess::updateOrCreate([
                'user_id' => $user->id,
                'branch_id' => $branch['id'],
            ], [
                'is_checked' => $branch['is_checked'] ?? false,
            ]);
        }

        DB::commit();
        CacheUser::forget();
        CacheBranchAccess::forget();

        return redirect()->route('user.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the User detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(User $user)
    {
        $params = [
            'user' => $user,
            'statuses' => $user->statuses,
        ];
        // dd($user);
        // die();

        return Inertia::render('Manage/User/Show', $params);
    }

    /**
     * Show the User edit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(User $user)
    {
        $access = BranchAccess::where('user_id', $user->id)->pluck('is_checked', 'branch_id')->toArray();

		$params = [
            'user' => $user,
            'roles' => CacheRole::get(),
            'access' => $access,
        ];

        return Inertia::render('Manage/User/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        $is_other = empty($request->type);
        $is_barista = $request->type == 'barista';
        $is_chef = $request->type == 'chef';
        $is_waiter = $request->type == 'waiter';
        $is_rider = $request->type == 'rider';

        $request->validate([
            'type' => ['nullable', 'string'],
            'role_id' => [$is_other ? 'required' : 'nullable', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8'],
            'mobile' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string', 'max:500'],
            'branches' => ['required', 'array'],
        ]);

        DB::beginTransaction();
        $user->role_id = $is_other ? $request->role_id : null;
        $user->is_barista = $is_barista;
        $user->is_chef = $is_chef;
        $user->is_waiter = $is_waiter;
        $user->is_rider = $is_rider;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->address = $request->address;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        foreach ($request->branches as $branch) {
            BranchAccess::updateOrCreate([
                'user_id' => $user->id,
                'branch_id' => $branch['id'],
            ], [
                'is_checked' => $branch['is_checked'] ?? false,
            ]);
        }

        DB::commit();
        CacheUser::forget();
        CacheBranchAccess::forget();

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();
        $user->delete();

        DB::commit();
        CacheUser::forget();

        return redirect()->route('user.index')->with('success', __('User removed successfully!'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  User  $user
     * @return Response
     */
    public function updateImage(Request $request, User $user)
    {
        $request->validate([
			'image_file'	=> ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'image_removed' => ['nullable', 'boolean'],
        ]);

		if ($request->image_removed) {
			$user->destroyImage();
			$user->update([ 'image' => "" ]);
			return back()->with('success', 'User Image removed!');
		}

		if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
			$image_path = $user->storeImage($request->file('image_file'), 'images/users');
			$user->update([ 'image' => $image_path ]);
			return back()->with('success', 'User Image uploaded!');
		}

        return back()->with('fail', 'User Image changing failed!');
	}

    /**
     * Change status the specified resource in storage.
     *
     * @return Response
     */
    public function updateStatus(Request $request, User $user)
    {
        if (! array_key_exists($request->status, $user->statuses)) {
            return back()->with('fail', 'Status changing request failed! Invalid status!');
        }

        if ($user->status == $request->status) {
            return back()->with('fail', 'Status already changed!');
        }

        DB::beginTransaction();
        $user->changeStatuses()->save(new Status([
            'previous_status' => $user->status ?? '',
            'changed_status' => $request->status,
            'user_id' => $request->user()->id,
        ]));
        $user->status = $request->status;
        $user->save();
        DB::commit();
        CacheUser::forget();

        return back()->with('success', 'Status changed to "'.$user->statuses[$request->status].'" successfully');
    }

    public function digest()
    {
        $users = CacheUser::get();
        $digest = CacheEmailDigest::get()->groupBy('user_id')->map->pluck('is_checked', 'branch_id')->toArray();

        $params = [
            'users' => $users,
            'digest' => $digest,
        ];

        return Inertia::render('Manage/User/EmailDigest', $params);
    }

    public function digestUpdate(Request $request, User $user)
    {
        $request->validate([
            'digest' => ['required', 'array'],
            'user_id' => ['required', 'exists:users,id'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        EmailDigest::updateOrCreate([
            'user_id' => $request->user_id,
            'branch_id' => $request->branch_id,
        ], [
            'is_checked' => $request->digest[$request->user_id][$request->branch_id],
        ]);

        CacheEmailDigest::forget();

        return back()->with('success', __('User Email Digest modified successfully!'));
    }

    /**
     * User login page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function login(User $user, Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

		Auth::loginUsingId($user->id);

        // return redirect()->intended(RouteServiceProvider::HOME);
        return redirect()->route('index');
    }
}
