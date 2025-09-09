<?php

namespace App\Http\Controllers\Manage;

use App\Http\Cache\CacheItem;
use App\Http\Cache\CacheItemCategory;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\ImageManager;

class ItemController extends Controller
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
        $items = CacheItem::get();

        $params = [
            'items' => $items,
        ];

        return Inertia::render('Manage/Item/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $item_categories = CacheItemCategory::get();

        $params = [
            'units' => (new Item)->units,
            'categories' => $item_categories,
        ];

        return Inertia::render('Manage/Item/Create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191', 'unique:items,name'],
            'unit' => ['required'],
            'avg_rate' => ['nullable', 'numeric'],
            'min_limit' => ['nullable', 'numeric'],
            'item_category_id' => ['required', 'exists:item_categories,id'],
        ]);

        DB::beginTransaction();
        $item = new Item;
        $item->name = $request->name;
        $item->unit = $request->unit;
        $item->avg_rate = $request->avg_rate ?? 0;
        $item->min_limit = $request->min_limit ?? 0;
        $item->item_category_id = $request->item_category_id;
        $item->save();

        DB::commit();
        CacheItem::forget();

        return redirect()->route('item.index')->with('success', __('Item added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Item $item)
    {
        $params = [
            'item' => $item,
        ];

        return Inertia::render('Manage/Item/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Item $item)
    {
        $item_categories = CacheItemCategory::get();
        $params = [
            'item' => $item,
            'units' => (new Item)->units,
            'categories' => $item_categories,
        ];

        return Inertia::render('Manage/Item/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'unit' => ['required'],
            'avg_rate' => ['nullable', 'numeric'],
            'min_limit' => ['nullable', 'numeric'],
            'item_category_id' => ['required', 'exists:item_categories,id'],
        ]);

        DB::beginTransaction();
        $item->name = $request->name;
        $item->unit = $request->unit;
        $item->avg_rate = $request->avg_rate ?? 0;
        $item->min_limit = $request->min_limit ?? 0;
        $item->item_category_id = $request->item_category_id;
        $item->update();

        if ($request->image_file) {
            if ($request->hasFile('image_file')) {
                if ($request->file('image_file')->isValid()) {
                    Storage::makeDirectory('thumbnails/images/');
                    $thumbnail_path = Storage::path('thumbnails/images/');

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
                    $item->images()->save($image);
                }
            }
        } else {
            if ($item->latest_image && $request->image_removed) {
                foreach ($item->images as $image) {
                    if (Storage::exists($image->path)) {
                        unlink(storage_path('app/public/thumbnails/'.$image->path));
                        unlink(storage_path('app/public/'.$image->path));
                    }
                    $image->delete();
                }
            }
        }

        DB::commit();
        CacheItem::forget();

        return back()->with('success', __('Item modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Item $item)
    {
        DB::beginTransaction();
        $item->delete();

        DB::commit();
        CacheItem::forget();

        return redirect()->route('item.index')->with('success', __('Item removed successfully!'));
    }
}
