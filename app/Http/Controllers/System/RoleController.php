<?php

namespace App\Http\Controllers\System;

use App\Helpers;
use App\Http\Cache\CacheRole;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RoleController extends Controller
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $roles = CacheRole::get();

        $params = [
            'roles' => $roles,
        ];

        return Inertia::render('System/Role/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $params = [];

        return Inertia::render('System/Role/Create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $role = new Role;
        $role->name = $request->name;

        if (! $role->save()) {
            return back()->with('fail', __('Role saving failed.'));
        }

        DB::commit();
        CacheRole::forget();

        return redirect()->route('role.index')->with('success', __('Role added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Role $role)
    {
        $params = [
            'role' => $role,
        ];

        return Inertia::render('System/Role/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Role $role)
    {
        $params = [
            'role' => $role,
        ];

        return Inertia::render('System/Role/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $role->name = $request->name;
        $role->update();

        DB::commit();
        CacheRole::forget();

        return back()->with('success', __('Role modified successfully!'));
    }

    public function permission(Role $role)
    {
        $permissions = config('permission');
        $role_permissions = $role->permissions;

        foreach ($permissions as $name => $groups) {
            foreach ($groups as $item => $route) {
                $route = ['is_checked' => $role_permissions[$name][$item]['is_checked'] ?? false];
                $permissions[$name][$item] = $route;
            }
        }

        $hiddenOptions = [];
        if (! Auth::user()->is_admin) {
            $hiddenOptions = [
                'data',
                'record_search',
                'report',
                'summary',
                'pos',
            ];
        }

        $params = [
            'role' => $role,
            'permissions' => $permissions,
            'hidden_options' => $hiddenOptions,
            'replaceable_words' => [
                'item' => Helpers::getSettingValueOf('item'),
                'product' => Helpers::getSettingValueOf('product'),
                'item_inventory' => Helpers::getSettingValueOf('item_inventory'),
                'product_inventory' => Helpers::getSettingValueOf('product_inventory'),
            ],
        ];

        return Inertia::render('System/Role/Permission', $params);
    }

    public function permissionUpdate(Request $request, Role $role)
    {
        if (! count($request->permissions)) {
            return back()->with('success', __('Permissions Not Selected'));
        }

        $request->validate([
            'permissions' => ['nullable'],
        ]);

        $role->permissions = $request->permissions;
        $role->update();

        CacheRole::forget();

        return back()->with('success', __('Permission modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Role $role)
    {
        $user_count = $role->users->count();
        if ($user_count > 0) {
            return back()->with('fail', __('Role removing denied. '.$user_count.' users found!'));
        }

        DB::beginTransaction();
        if (! $role->delete()) {
            return back()->with('fail', __('Role removing failed.'));
        }

        DB::commit();
        CacheRole::forget();

        return redirect()->route('role.index')->with('success', __('Role removed successfully!'));
    }
}
