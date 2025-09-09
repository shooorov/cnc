<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageTrait
{
	protected $default_image = '/img/avatar.jpg';
	
	protected $directory = 'images';

    /**
     * Get the Model's image.
     */
    public function latest_image()
    {
        return $this->morphOne(Image::class, 'imageable')->latestOfMany();
    }

    /**
     * Get all of the Model's images.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the Model's image.
     */
    public function defaultImageUrl(): Attribute
    {
        return Attribute::get(fn () => asset($this->default_image));
    }

    /**
     * Get the image url.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function imageUrl(): Attribute
    {
        // $path = null;
        // if ($this->latest_image && Storage::exists('thumbnails/' . $this->latest_image->path)) {
        //     $path = Storage::url('thumbnails/' . $this->latest_image->path);
        // }

		$path = $this->latest_image && $this->latest_image?->path && Storage::disk('public')->exists($this->latest_image?->path) 
			? Storage::disk('public')->url($this->latest_image->path) 
			: $this->default_image_url;

        return Attribute::get(fn () => $path);
    }

    /**
     * Store image to imageable.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
	public function storeImage($imageFile) : String
	{
		$extension = $imageFile->extension();
		$file_name = Str::random(16) . '.' . $extension;
		$image_path = $imageFile->storeAs($this->directory, $file_name, 'public');

		$image = new Image(['path' => $image_path]);
		$this->images()->save($image);
		return $image_path;
	}

    /**
     * Delete image from imageable.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
	public function destroyImage() : void
	{
		// foreach ($this->images as $image) {
		// 	if (Storage::disk('public')->exists($image->path)) {
		// 		unlink(Storage::disk('public')->path($image->path));
		// 	}
		// 	$image->delete();
		// }

		if($this->latest_image && !empty($this->latest_image?->path)) {
			$image = new Image(['path' => ""]);
			$this->images()->save($image);
		}

	}
}
