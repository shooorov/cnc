<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheOtherSaleCategory;
use App\Http\Controllers\Controller;
use App\Models\OtherSaleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OtherSaleCategoryController extends Controller
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
        $other_sale_categories = CacheOtherSaleCategory::get();

        $params = [
            'other_sale_categories' => $other_sale_categories,
        ];

        return Inertia::render('System/OtherSaleCategory/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $other_sale_categories = CacheOtherSaleCategory::get()->filter(function ($category) {
            return is_null($category->other_sale_category_id);
        });

        $params = [
            'other_sale_categories' => $other_sale_categories,
        ];

        return Inertia::render('System/OtherSaleCategory/Create', $params);
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
            'other_sale_category_id' => ['nullable', 'exists:other_sale_categories,id'],
        ]);

        DB::beginTransaction();
        $other_sale_category = new OtherSaleCategory;
        $other_sale_category->name = $request->name;
        $other_sale_category->other_sale_category_id = $request->other_sale_category_id;
        $other_sale_category->save();

        DB::commit();
        CacheOtherSaleCategory::forget();

        return redirect()->route('other_sale_category.index')->with('success', __('OtherSaleCategory added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(OtherSaleCategory $other_sale_category)
    {
        $params = [
            'other_sale_category' => $other_sale_category,
        ];

        return Inertia::render('System/OtherSaleCategory/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(OtherSaleCategory $other_sale_category)
    {
        $other_sale_categories = CacheOtherSaleCategory::get()->filter(function ($category) {
            return is_null($category->other_sale_category_id);
        });

        $params = [
            'other_sale_category' => $other_sale_category,
            'other_sale_categories' => $other_sale_categories,
        ];

        return Inertia::render('System/OtherSaleCategory/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, OtherSaleCategory $other_sale_category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'other_sale_category_id' => ['nullable', 'exists:other_sale_categories,id'],
        ]);

        DB::beginTransaction();
        $other_sale_category->name = $request->name;
        $other_sale_category->other_sale_category_id = $request->other_sale_category_id;
        $other_sale_category->update();

        DB::commit();
        CacheOtherSaleCategory::forget();

        return back()->with('success', __('OtherSaleCategory modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(OtherSaleCategory $other_sale_category)
    {
        DB::beginTransaction();
        $other_sale_category->delete();

        DB::commit();
        CacheOtherSaleCategory::forget();

        return redirect()->route('other_sale_category.index')->with('success', __('OtherSaleCategory removed successfully!'));
    }
}
