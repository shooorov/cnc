<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheProductCategory;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\ImageManager;

class ProductCategoryController extends Controller
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
        $product_categories = CacheProductCategory::get();

        $params = [
            'product_categories' => $product_categories,
        ];

        return Inertia::render('System/ProductCategory/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $product_categories = CacheProductCategory::get()->filter(function ($category) {
            return is_null($category->product_category_id);
        });

        $params = [
            'product_categories' => $product_categories,
            'types' => [
                ['id' => 'chef',    'name' => 'Chef'],
                ['id' => 'barista', 'name' => 'Barista'],
            ],
        ];

        return Inertia::render('System/ProductCategory/Create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191', 'unique:product_categories,name'],
            'type' => ['required', 'string'],
            'is_platter' => ['nullable', 'boolean'],
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
        ]);

        DB::beginTransaction();
        $product_category = new ProductCategory;
        $product_category->name = $request->name;
        $product_category->type = $request->type;
        $product_category->is_platter = $request->is_platter ?? 0;
        $product_category->product_category_id = $request->product_category_id;
        $product_category->save();

        DB::commit();

        CacheProductCategory::forget();

        return redirect()->route('product_category.index')->with('success', __('Product Category added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(ProductCategory $product_category)
    {
        $params = [
            'product_category' => $product_category,
        ];

        return Inertia::render('System/ProductCategory/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(ProductCategory $product_category)
    {
        $product_categories = CacheProductCategory::get()->filter(function ($category) {
            return is_null($category->product_category_id);
        });

        $params = [
            'product_category' => $product_category,
            'product_categories' => $product_categories,
            'types' => [
                ['id' => 'chef',    'name' => 'Chef'],
                ['id' => 'barista', 'name' => 'Barista'],
            ],
        ];

        return Inertia::render('System/ProductCategory/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, ProductCategory $product_category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'type' => ['required', 'string'],
            'is_platter' => ['nullable', 'boolean'],
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
        ]);

        DB::beginTransaction();
        $product_category->name = $request->name;
        $product_category->type = $request->type;
        $product_category->is_platter = $request->is_platter ?? 0;
        $product_category->product_category_id = $request->product_category_id;
        $product_category->update();

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
                    $product_category->images()->save($image);
                }
            }
        } else {
            if ($product_category->latest_image && $request->image_removed) {
                foreach ($product_category->images as $image) {
                    if (Storage::exists($image->path)) {
                        unlink(storage_path('app/public/thumbnails/'.$image->path));
                        unlink(storage_path('app/public/'.$image->path));
                    }
                    $image->delete();
                }
            }
        }

        DB::commit();
        CacheProductCategory::forget();

        return back()->with('success', __('Product Category modified successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function specification()
    {
        $product_categories = CacheProductCategory::get()->where('is_platter', false)->whereNull('product_category_id');

        foreach ($product_categories as $category) {
            $category->specification = [
                $category->type == 'chef',
                $category->type == 'barista',
            ];
        }
        $params = [
            'product_categories' => $product_categories,
            'types' => [
                ['id' => 'chef',    'name' => 'Chef'],
                ['id' => 'barista', 'name' => 'Barista'],
            ],
        ];

        return Inertia::render('System/ProductCategory/Specification', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function specificationUpdate(Request $request)
    {
        $types = [
            ['id' => 'chef',    'name' => 'Chef'],
            ['id' => 'barista', 'name' => 'Barista'],
        ];

        $request->validate([
            'product_categories' => ['required', 'array'],
        ]);

        DB::beginTransaction();

        foreach ($request->product_categories as $category) {
            $product_category = ProductCategory::find($category['id']);
            $product_category->update(['type' => $category['type']]);
        }

        DB::commit();
        CacheProductCategory::forget();

        return back()->with('success', __('Branch Access modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(ProductCategory $product_category)
    {
        DB::beginTransaction();
        $product_category->delete();

        DB::commit();
        CacheProductCategory::forget();

        return redirect()->route('product_category.index')->with('success', __('Product Category removed successfully!'));
    }
}
