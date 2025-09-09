<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheUnit;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UnitController extends Controller
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
            'units' => CacheUnit::get(),
        ];

        return Inertia::render('System/Unit/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $params = [
            'units' => CacheUnit::get(),
        ];

        return Inertia::render('System/Unit/Create', $params);
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
            'short' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $unit = new Unit;
        $unit->name = $request->name;
        $unit->short = $request->short;
        $unit->save();

        DB::commit();
        CacheUnit::forget();

        return redirect()->route('unit.index')->with('success', __('Unit added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Unit $unit)
    {
        $params = [
            'unit' => $unit,
        ];

        return Inertia::render('System/Unit/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Unit $unit)
    {
        $unit->conversions;
        $params = [
            'unit' => $unit,
            'units' => CacheUnit::get(),
        ];

        return Inertia::render('System/Unit/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'short' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $unit->name = $request->name;
        $unit->short = $request->short;
        $unit->update();

        $previous_conversions = $unit->conversions->pluck('to_unit_id')->toArray();
        foreach ($request->group_items ?? [] as $item) {
            if ($item['to_unit_id'] != null && $item['factor'] != null && $item['factor'] > 0) {
                if (($key = array_search($item['to_unit_id'], $previous_conversions)) !== false) {
                    unset($previous_conversions[$key]);
                }

                UnitConversion::updateOrCreate([
                    'from_unit_id' => $unit->id,
                    'to_unit_id' => $item['to_unit_id'],
                ], [
                    'factor' => $item['factor'],
                ]);

                UnitConversion::updateOrCreate([
                    'from_unit_id' => $item['to_unit_id'],
                    'to_unit_id' => $unit->id,
                ], [
                    'factor' => 1 / $item['factor'],
                ]);
            }
        }
        UnitConversion::where('from_unit_id', $unit->id)->whereIn('to_unit_id', $previous_conversions)->delete();

        DB::commit();
        CacheUnit::forget();

        return back()->with('success', __('Unit modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Unit $unit)
    {
        DB::beginTransaction();
        $unit->delete();

        DB::commit();
        CacheUnit::forget();

        return redirect()->route('unit.index')->with('success', __('Unit removed successfully!'));
    }
}
