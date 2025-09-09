<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheItemCategory;
use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ItemCategoryController extends Controller
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
        $item_categories = CacheItemCategory::get();

        $params = [
            'item_categories' => $item_categories,
        ];

        return Inertia::render('System/ItemCategory/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $item_categories = CacheItemCategory::get()->filter(function ($category) {
            return is_null($category->item_category_id);
        });

        $params = [
            'item_categories' => $item_categories,
        ];

        return Inertia::render('System/ItemCategory/Create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191', 'unique:item_categories,name'],
            'item_category_id' => ['nullable', 'exists:item_categories,id'],
        ]);

        DB::beginTransaction();
        $item_category = new ItemCategory;
        $item_category->name = $request->name;
        $item_category->item_category_id = $request->item_category_id;
        $item_category->save();

        DB::commit();
        CacheItemCategory::forget();

        return redirect()->route('item_category.index')->with('success', __('ItemCategory added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(ItemCategory $item_category)
    {
        $params = [
            'item_category' => $item_category,
        ];

        return Inertia::render('System/ItemCategory/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(ItemCategory $item_category)
    {
        $item_categories = CacheItemCategory::get()->filter(function ($category) {
            return is_null($category->item_category_id);
        });

        $params = [
            'item_category' => $item_category,
            'item_categories' => $item_categories,
        ];

        return Inertia::render('System/ItemCategory/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, ItemCategory $item_category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'item_category_id' => ['nullable', 'exists:item_categories,id'],
        ]);

        DB::beginTransaction();
        $item_category->name = $request->name;
        $item_category->item_category_id = $request->item_category_id;
        $item_category->update();

        DB::commit();
        CacheItemCategory::forget();

        return back()->with('success', __('ItemCategory modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(ItemCategory $item_category)
    {
        DB::beginTransaction();
        $item_category->delete();

        DB::commit();
        CacheItemCategory::forget();

        return redirect()->route('item_category.index')->with('success', __('ItemCategory removed successfully!'));
    }
}
