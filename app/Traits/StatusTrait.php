<?php

namespace App\Traits;

use App\Models\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait StatusTrait
{
    public $default_status = 'active';

	/**
     * Get the Model's status.
     */
    public function latest_status()
    {
        return $this->morphOne(Status::class, 'statusable')->latestOfMany();
    }

    /**
     * Get all of the Model's statuses.
     */
    public function changeStatuses()
    {
        return $this->morphMany(Status::class, 'statusable');
    }

    /**
     * Get the Trait status.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    // protected function status(): Attribute
    // {
    //     return Attribute::get(fn () => $this->latest_status?->changed_status ?? $this->default_status ?? 'pending');
    // }

    // /**
    //  * Get the model's statues.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Casts\Attribute
    //  */
    // protected function statuses(): Attribute
    // {
    //     return Attribute::get(fn () => $this->statuses);
    // }
}
