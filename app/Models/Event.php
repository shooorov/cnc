<?php

namespace App\Models;

use App\Models\Scopes\UseBranchScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'start_time' => 'datetime:Y-m-d H:i:s',
        'end_time' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'title',
        'time_duration',
        'date_format',
        'end_time_format',
        'start_time_format',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new UseBranchScope);
    }

    /**
     * Get the time Duration.
     */
    public function timeDuration(): Attribute
    {
        $startTime = now()->parse($this->end_time);
        $finishTime = now()->parse($this->start_time);
        $totalDuration = $finishTime->diffInSeconds($startTime);
        $totalDuration = gmdate('H:i', $totalDuration);

        return Attribute::get(fn () => $totalDuration);
    }

    /**
     * Get the title.
     */
    public function title(): Attribute
    {
        $title = $this->name.' at '.$this->start_time->format('h:i a').' for '.$this->time_duration;

        return Attribute::get(fn () => $title);
    }

    /**
     * Get the date format.
     */
    public function dateFormat(): Attribute
    {
        return Attribute::get(fn () => $this->date?->format('Y-m-d'));
    }

    /**
     * Get the end time format.
     */
    public function endTimeFormat(): Attribute
    {
        return Attribute::get(fn () => $this->date?->format('h:i a'));
    }

    /**
     * Get the start time format.
     */
    public function startTimeFormat(): Attribute
    {
        return Attribute::get(fn () => $this->date?->format('h:i a'));
    }
}
