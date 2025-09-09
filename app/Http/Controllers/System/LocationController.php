<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheLocation;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LocationController extends Controller
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
        $locations = CacheLocation::get();

        $params = [
            'locations' => $locations,
        ];

        return Inertia::render('System/Location/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $locations = CacheLocation::get()->values();

        $params = [
            'locations' => $locations,
        ];

        return Inertia::render('System/Location/Create', $params);
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
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        DB::beginTransaction();
        $location = new Location;
        $location->name = $request->name;
        $location->branch_id = $request->branch_id;
        $location->save();

        DB::commit();
        CacheLocation::forget();

        return redirect()->route('location.index')->with('success', __('Location added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Location $location)
    {
        $params = [
            'location' => $location,
        ];

        return Inertia::render('System/Location/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Location $location)
    {
        $params = [
            'location' => $location,
        ];

        return Inertia::render('System/Location/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        DB::beginTransaction();
        $location->name = $request->name;
        $location->branch_id = $request->branch_id;
        $location->update();

        DB::commit();
        CacheLocation::forget();

        return back()->with('success', __('Location modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Location $location)
    {
        DB::beginTransaction();
        $location->delete();

        DB::commit();
        CacheLocation::forget();

        return redirect()->route('location.index')->with('success', __('Location removed successfully!'));
    }
}
