<?php

namespace App\Http\Controllers\Manage;

use App\Http\Cache\CacheTopping;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\ImageManager;

class ToppingController extends Controller
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
     * Show the Topping list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $params = [
            'toppings' => CacheTopping::get(),
        ];

        return Inertia::render('Manage/Topping/Index', $params);
    }

    /**
     * Show the Topping create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $params = [];

        return Inertia::render('Manage/Topping/Create', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @param  Topping  $topping
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $topping = new Topping;
        $topping->name = $request->name;
        $topping->description = $request->description;
        $topping->save();

        DB::commit();

        return redirect()->route('topping.index')->with('success', 'Topping created successfully.');
    }

    /**
     * Show the Topping detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Topping $topping)
    {
        $params = [
            'topping' => $topping,
        ];

        return Inertia::render('Manage/Topping/Show', $params);
    }

    /**
     * Show the Topping edit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(Topping $topping)
    {
        $params = [
            'topping' => $topping,
        ];

        return Inertia::render('Manage/Topping/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Topping $topping)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $topping->name = $request->name;
        $topping->description = $request->description;
        $topping->update();

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
                    $topping->images()->save($image);
                }
            }
        } else {
            if ($topping->latest_image && $request->image_removed) {
                foreach ($topping->images as $image) {
                    if (Storage::exists($image->path)) {
                        unlink(storage_path('app/public/thumbnails/'.$image->path));
                        unlink(storage_path('app/public/'.$image->path));
                    }
                    $image->delete();
                }
            }
        }

        DB::commit();

        return back()->with('success', 'Topping updated successfully.');
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Topping $topping)
    {
        DB::beginTransaction();
        $topping->delete();
        DB::commit();

        return redirect()->route('topping.index')->with('success', __('Topping removed successfully!'));
    }
}
