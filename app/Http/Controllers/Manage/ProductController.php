<?php

namespace App\Http\Controllers\Manage;

use App\Http\Cache\CacheItem;
use App\Http\Cache\CacheOrderProductQuantity;
use App\Http\Cache\CachePlatter;
use App\Http\Cache\CacheProduct;
use App\Http\Cache\CacheProductCategory;
use App\Http\Cache\CacheProductItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product as ResourcesProduct;
use App\Models\Image;
use App\Models\Item;
use App\Models\Platter;
use App\Models\Status;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\UseRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Mpdf\Tag\Tr;


class ProductController extends Controller
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
     * Show the Product list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $category_id = $request->category_id;
        $products = CacheProduct::get();
        $products = $products->when($category_id, function ($products, $category_id) {
            return $products->where('product_category_id', $category_id);
        })->values();

        $end_date = now()->parse($request->end_date ?? now())->format('Y-m-d');
        $start_date = now()->parse($request->start_date ?? now()->subDay())->format('Y-m-d');
        $sold_products = CacheOrderProductQuantity::get($start_date, $end_date)->pluck('product_quantity', 'product_id');

        $params = [
            'products' => $products,
            'sold_products' => $sold_products,
            'categories' => CacheProductCategory::get()->whereNull('product_category_id')->values(),

            'filter' => [
                'category_id' => $category_id,
                'end_date' => $end_date,
                'start_date' => $start_date,
            ],
        ];

        return Inertia::render('Manage/Product/Index', $params);
    }

    /**
     * Show the Product create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $product_categories = CacheProductCategory::get();

        $params = [
            'categories' => $product_categories,
        ];

        return Inertia::render('Manage/Product/Create', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @param  Product  $product
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191', 'unique:products,name'],
            'english_name' => ['nullable', 'string', 'max:191', 'unique:products,english_name'],
            'code' => ['required', 'string', 'unique:products'],
            'rate' => ['nullable', 'numeric'],
            'discount' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string', 'max:500'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
        ]);

        DB::beginTransaction();
        $product = new Product;
        $product->name = $request->name;
        $product->english_name = $request->english_name;
        $product->code = $request->code;
        $product->rate = $request->rate;
        $product->discount = $request->discount;
        $product->description = $request->description;
        $product->product_category_id = $request->product_category_id;
        $product->save();

        DB::commit();
        CacheProduct::forget();
        CacheProductItem::forget();

        return redirect()->route('product.edit', $product->id)->with('success', 'Product created successfully.');
    }

    /**
     * Show the Product detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Product $product)
    {
        $params = [
            'product' => $product,
        ];

        return Inertia::render('Manage/Product/Show', $params);
    }

    /**
     * Show the Product edit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(Product $product)
    {
        $items = CacheItem::get();
        // $products = CacheProduct::get()->where('id', '!=', $product->id)->reject(function ($product, $key) {
        //     return $product->is_platter;
        // })->values();
        $products = CacheProduct::get()->where('id', '=', $product->id)->reject(function ($product, $key) {
            return $product->is_platter;
        })->values();
        $product_categories = CacheProductCategory::get();

        $params = [
            'units' => $product->units,
            'items' => $items,
            'products' => $products,
            'statuses' =>  $product->statuses,
            'product' => new ResourcesProduct($product),
            'categories' => $product_categories,
        ];
        // dd($params);
        // die();

        return Inertia::render('Manage/Product/Edit', $params);
    }

    public function updateStatus(Request $request, Product $product)
    {
        if (! array_key_exists($request->status, $product->statuses)) {
            return back()->with('fail', 'Status changing request failed! Invalid status!');
        }

        if ($product->status == $request->status) {
            return back()->with('fail', 'Status already changed!');
        }

        DB::beginTransaction();
        try {
            //code...
            $product->changeStatuses()->save(new Status([
                'previous_status' => $product->status ?? '',
                'changed_status' => $request->status,
                'user_id' => $request->user()->id,
            ]));
        } catch (\Throwable $th) {
            //throw $th;
        }

        $product->status = $request->status;
        $product->save();
        DB::commit();
        CacheProduct::forget();

        return back()->with('success', 'Status changed to "' . $product->statuses[$request->status] . '" successfully');
    }
    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'english_name' => ['nullable', 'string', 'max:191'],
            'code' => ['required', 'string',  Rule::unique('products')->ignore($product)],
            'rate' => ['nullable', 'numeric'],
            'discount' => ['nullable', 'numeric'],
            'number_of_persons' => ['nullable', 'numeric'],
            'vat_applicable' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
        ]);

        DB::beginTransaction();

        $is_platter = ProductCategory::find($request->product_category_id)->is_platter;

        $product->name = $request->name;
        $product->english_name = $request->english_name;
        $product->code = $request->code;
        $product->rate = $request->rate;
        $product->discount = $request->discount;
        $product->number_of_persons = $request->number_of_persons ?? 1;
        $product->description = $request->description;
        $product->vat_applicable = $request->vat_applicable;
        $product->product_category_id = $request->product_category_id;
        $product->production_cost = $request->production_cost;

        $product->margin_amount = $is_platter ? null : $request->margin_amount;
        $product->margin_percent = $is_platter ? null : $request->margin_percent;

        $product->update();

        $this->handleProductImage($request, $product);

        if ($is_platter) {
            $previous_items = $product->platters->pluck('id')->toArray();
            foreach ($request->platter_items ?? [] as $item) {
                if ($item['item_id']) {
                    $id = array_key_exists('id', $item) ? $item['id'] : null;
                    if (($key = array_search($id, $previous_items)) !== false) {
                        unset($previous_items[$key]);
                    }

                    Platter::updateOrCreate([
                        'id' => $id,
                        'product_id' => $product->id,
                    ], [
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                    ]);
                }
            }
            Platter::where('product_id', $product->id)->whereIn('id', $previous_items)->delete();
        } else {
            $previous_items = $product->items->pluck('id')->toArray();
            foreach ($request->group_items ?? [] as $item) {
                if ($item['quantity_use'] > 0 && $item['item_id'] && $selected_item = Item::find($item['item_id'])) {
                    $id = array_key_exists('id', $item) ? $item['id'] : null;
                    if (($key = array_search($id, $previous_items)) !== false) {
                        unset($previous_items[$key]);
                    }
                    $unit = $selected_item->unit;
                    if ($unit != 'pcs') {
                        $item['quantity_use'] = $item['quantity_use'] / 1000;
                    }

                    ProductItem::updateOrCreate([
                        'id' => $id,
                        'product_id' => $product->id,
                    ], [
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity_use'],
                    ]);
                }
            }
            ProductItem::where('product_id', $product->id)->whereIn('id', $previous_items)->delete();
        }

        DB::commit();
        $is_platter ? CachePlatter::forget() : null;
        CacheProduct::forget();
        CacheProductItem::forget();

        return back()->with('success', 'Product updated successfully.');
    }

    /**
     * Handle product image upload and removal
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function handleProductImage(Request $request, Product $product)
    {
        // Validation
        $request->validate([
            'photo_file' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'photo_removed' => ['required', 'boolean'],
        ]);

        $relative_path = 'images';
        $relative_thumbnail = 'thumbnails/' . $relative_path;

        // Ensure directories exist
        Storage::makeDirectory($relative_path);
        Storage::makeDirectory($relative_thumbnail);

        $original_thumbnail = Storage::path($relative_thumbnail);

        // Handle new image upload
        if ($request->hasFile('photo_file') && $request->file('photo_file')->isValid()) {
            $extension = $request->photo_file->extension();
            $file_name = $product->id . '-' . $product->name . '-image.' . $extension;
            $image_path = $request->photo_file->storeAs($relative_path, $file_name, 'public');

            $thumbnail_file_path = $original_thumbnail . '/' . $file_name;

            $manager = new ImageManager(new Driver());
            $image_mng = $manager->read(Storage::path($image_path));
            $image_mng->scale(width: 134);
            $image_mng->toPng()->save($thumbnail_file_path);

            $image = UseRecord::makeImage($image_path, $product);

            if (! $image) {
                return back()->with('fail', 'Image update failed!');
            }
        }
        // Handle image removal
        elseif ($product->latest_image && $request->photo_removed) {
            foreach ($product->images as $image) {
                if (Storage::exists($image->path)) {
                    Storage::delete($image->path);
                }

                $thumbnail_path = 'thumbnails/' . $image->path;
                if (Storage::exists($thumbnail_path)) {
                    Storage::delete($thumbnail_path);
                }

                $image->delete();
            }
        }
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        $product->delete();
        DB::commit();
        CacheProduct::forget();

        return redirect()->route('product.index')->with('success', __('Product removed successfully!'));
    }
}
