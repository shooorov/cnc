<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheStuff;
use App\Http\Controllers\Controller;
use App\Models\Stuff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StuffController extends Controller
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
        $params = [
            'stuffs' => CacheStuff::get(),
        ];

        return Inertia::render('System/Stuff/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $params = [];

        return Inertia::render('System/Stuff/Create', $params);
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
            'description' => ['nullable', 'string', 'max:191'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        DB::beginTransaction();
        $stuff = new Stuff;
        $stuff->name = $request->name;
        $stuff->description = $request->description;
        $stuff->branch_id = $request->branch_id;
        $stuff->save();

        DB::commit();
        CacheStuff::forget();

        return redirect()->route('stuff.index')->with('success', __('Stuff added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Stuff $stuff)
    {
        $params = [
            'stuff' => $stuff,
        ];

        return Inertia::render('System/Stuff/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Stuff $stuff)
    {
        $params = [
            'stuff' => $stuff,
        ];

        return Inertia::render('System/Stuff/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Stuff $stuff)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:191'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        DB::beginTransaction();

        $stuff->name = $request->name;
        $stuff->description = $request->description;
        $stuff->branch_id = $request->branch_id;
        $stuff->update();

        DB::commit();
        CacheStuff::forget();

        return back()->with('success', __('Stuff modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Stuff $stuff)
    {
        DB::beginTransaction();
        $stuff->delete();

        DB::commit();
        CacheStuff::forget();

        return redirect()->route('stuff.index')->with('success', __('Stuff removed successfully!'));
    }
}
