<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheOnlineProduct;
use App\Http\Cache\CacheOnlineProductCategory;
use App\Http\Cache\CacheProduct;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\OnlineProduct;
use App\Models\OnlineProductCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\ImageManager;

class OnlineProductCategoryController extends Controller
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
        $online_product_categories = CacheOnlineProductCategory::get()->sortBy('serial')->values();

        $params = [
            'online_product_categories' => $online_product_categories,
        ];

        return Inertia::render('System/OnlineProductCategory/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $params = [];

        return Inertia::render('System/OnlineProductCategory/Create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191', 'unique:online_product_categories,name'],
        ]);

        DB::beginTransaction();
        $online_product_category = new OnlineProductCategory();
        $online_product_category->name = $request->name;
        $online_product_category->save();

        DB::commit();

        CacheOnlineProductCategory::forget();

        return redirect()->route('online_product_category.index')->with('success', __('Online Product Category added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(OnlineProductCategory $online_product_category)
    {
        $params = [
            'online_product_category' => $online_product_category,
        ];

        return Inertia::render('System/OnlineProductCategory/Show', $params);
    }

    /**
     * Display the specified resource.
     *
     * @param  OnlineProductCategory  $online_product_category
     * @return Response
     */
    public function positioning(Request $request)
    {
        $set_position = $request->position;
        $category = OnlineProductCategory::find($request->category_id);
        $category_id = $request->category_id;

        $ids_array = OnlineProductCategory::orderBy('serial')->pluck('id')->toArray();
        $index = array_search($category_id, $ids_array);
        $inserted = $ids_array[$index];
        unset($ids_array[$index]);

        if ($set_position == 'top') {
            $ids_array = array_merge([$inserted], $ids_array);
        } elseif ($set_position == 'up') {
            array_splice($ids_array, ($index - 1), 0, $inserted);
        } elseif ($set_position == 'down') {
            array_splice($ids_array, ($index + 1), 0, $inserted);
        } elseif ($set_position == 'bottom') {
            $ids_array = array_merge($ids_array, [$inserted]);
        }

        $i = 0;
        foreach ($ids_array as $id) {
            OnlineProductCategory::updateOrCreate(
                ['id' => $id],
                ['serial' => ++$i]
            );
        }

        CacheOnlineProductCategory::forget();

        return back()->with('success', $category->name." Position Moved to $set_position successfully.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(OnlineProductCategory $online_product_category)
    {
        // add child
        $online_product_category->items;

        $params = [
            'products' => CacheProduct::get(),
            'category' => $online_product_category,
        ];

        return Inertia::render('System/OnlineProductCategory/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, OnlineProductCategory $online_product_category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $online_product_category->name = $request->name;
        $online_product_category->update();

        if ($request->image_file) {
            if ($request->hasFile('image_file')) {
                if ($request->file('image_file')->isValid()) {
                    Storage::makeDirectory('thumbnails/product-categories/');
                    $thumbnail_path = Storage::path('thumbnails/product-categories/');

                    $extension = $request->image_file->extension();
                    $file_name = Str::random(10).'.'.$extension;
                    $image_path = $request->image_file->storeAs('images', $file_name, 'public');

                    // create image manager with desired driver
                    $manager = new ImageManager(config('image.driver'));

                    // read image from file system
                    $image_mng = $manager->make(Storage::path($image_path));

                    // resize image proportionally to 300px width
                    $image_mng->scale(width: 134);

                    // save modified image in new format
                    $image_mng->toPng()->save($thumbnail_path.'/'.$file_name);

                    $image = new Image(['path' => $image_path]);
                    $online_product_category->images()->save($image);
                }
            }
        } else {
            if ($online_product_category->latest_image && $request->image_removed) {
                foreach ($online_product_category->images as $image) {
                    if (Storage::exists($image->path)) {
                        unlink(storage_path('app/public/thumbnails/'.$image->path));
                        unlink(storage_path('app/public/'.$image->path));
                    }
                    $image->delete();
                }
            }
        }

        OnlineProduct::where('online_product_category_id', $online_product_category->id)->delete();

        foreach ($request->group_items ?? [] as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                OnlineProduct::Create([
                    'product_id' => $product->id,
                    'online_product_category_id' => $online_product_category->id,
                ]);
            }
        }

        DB::commit();
        CacheOnlineProduct::forget();
        CacheOnlineProductCategory::forget();

        return back()->with('success', __('Online Product Category modified successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function order()
    {
        $online_product_categories = CacheOnlineProductCategory::get()->sortBy('serial')->values();

        $params = [
            'online_product_categories' => $online_product_categories,
        ];

        return Inertia::render('System/OnlineProductCategory/Order', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function orderUpdate(Request $request)
    {
        $request->validate([
            'product_categories' => ['required', 'array'],
        ]);

        DB::beginTransaction();

        foreach ($request->product_categories as $category) {
            $online_product_category = OnlineProductCategory::find($category['id']);
            $online_product_category->update(['type' => $category['type']]);
        }

        DB::commit();
        CacheOnlineProductCategory::forget();

        return back()->with('success', __('Branch Access modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(OnlineProductCategory $online_product_category)
    {
        DB::beginTransaction();
        $online_product_category->delete();

        DB::commit();
        CacheOnlineProductCategory::forget();

        return redirect()->route('online_product_category.index')->with('success', __('Online Product Category removed successfully!'));
    }
}
