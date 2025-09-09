<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheDesignation;
use App\Http\Cache\CacheRole;
use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DesignationController extends Controller
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
        $designations = CacheDesignation::get();

        $params = [
            'designations' => $designations,
        ];

        return Inertia::render('System/Designation/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $params = [];

        return Inertia::render('System/Designation/Create', $params);
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
        $designation = new Designation;
        $designation->name = $request->name;
        $designation->save();

        DB::commit();
        CacheDesignation::forget();

        return redirect()->route('designation.index')->with('success', __('Designation added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Designation $designation)
    {
        $params = [
            'designation' => $designation,
        ];

        return Inertia::render('System/Designation/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Designation $designation)
    {
        $params = [
            'designation' => $designation,
        ];

        return Inertia::render('System/Designation/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();

        if ($request->name != $designation->name && $designation->in_role) {
            $role = Role::where('name', $designation->name)->first();
            $role->name = $request->name;
            $role->update();
        }

        $designation->name = $request->name;
        $designation->update();

        DB::commit();
        CacheDesignation::forget();

        return back()->with('success', __('Designation modified successfully!'));
    }

    /**
     * Making Role the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function role(Designation $designation)
    {
        if ($designation->in_role) {
            return back()->with('fail', __('Role found!'));
        }

        $role = new Role;
        $role->slug = Str::of($designation->name)->slug();
        $role->name = $designation->name;
        $role->short = Str::of($designation->name)->slug();
        $role->save();

        CacheRole::forget();

        return back()->with('success', __('Designation Added as Role successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Designation $designation)
    {
        DB::beginTransaction();
        $designation->delete();

        DB::commit();
        CacheDesignation::forget();

        return redirect()->route('designation.index')->with('success', __('Designation removed successfully!'));
    }
}
