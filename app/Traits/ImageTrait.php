<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageTrait
{
    /**
     * Default fallback image.
     */
    protected string $default_image = '/img/avatar.jpg';

    /**
     * Directory to store images.
     */
    protected string $directory = 'images';

    /**
     * Get the model's latest image.
     */
    public function latest_image()
    {
        return $this->morphOne(Image::class, 'imageable')->latestOfMany();
    }

    /**
     * Get all of the model's images. also storing
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the model's default image URL.
     */
    public function defaultImageUrl(): Attribute
    {
        return Attribute::get(fn() => asset($this->default_image));
    }

    /**
     * Get the image URL.
     */
    public function imageUrl(): Attribute
    {
        // Alternative thumbnail logic (keep for later use)
        // $path = null;
        // if ($this->latest_image && Storage::exists('thumbnails/' . $this->latest_image->path)) {
        //     $path = Storage::url('thumbnails/' . $this->latest_image->path);
        // }

        $path = $this->latest_image?->path && Storage::disk('public')->exists($this->latest_image->path)
            ? Storage::url($this->latest_image->path)
            : $this->default_image_url;

        return Attribute::get(fn() => $path);
    }

    /**
     * Store image to imageable.
     */
    public function storeImage($imageFile): string
    {
        $imagePath = $imageFile->storeAs(
            $this->directory,
            Str::random(16) . '.' . $imageFile->extension(),
            'public'
        );

        $this->images()->save(new Image(['path' => $imagePath]));

        return $imagePath;
    }

    /**
     * Delete image from imageable.
     *
     * By default, this does a "soft remove" by attaching a blank image path.
     * Use the commented code below for a full hard delete (removes files).
     */
    public function destroyImage(): void
    {
        // Hard delete all images (use if you want actual file removal):
        // foreach ($this->images as $image) {
        //     if (Storage::disk('public')->exists($image->path)) {
        //         unlink(Storage::disk('public')->path($image->path));
        //     }
        //     $image->delete();
        // }

        // Default: keep record but blank out path (soft remove)
        if ($this->latest_image?->path) {
            $this->images()->save(new Image(['path' => '']));
        }
    }
}
