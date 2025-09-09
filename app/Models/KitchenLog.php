<?php

namespace App\Models;

use App\Http\Cache\CacheProduction;
use App\Http\Cache\CacheUser;
use App\Models\Scopes\UseBranchScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class KitchenLog extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'productions' => 'array',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'user_name',
        'time_duration',
        'productions',
        'total_item',
        'total_production',
        'ended_at_format',
        'started_at_format',
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
     * Get the productions.
     */
    public function productions(): Attribute
    {
        $productions = [];
        foreach (CacheProduction::get()->where('kitchen_log_id', $this->id) as $production) {
            array_push($productions, $production);
        }

        return Attribute::get(fn () => $productions);
    }

    /**
     * Get the productions_count.
     */
    public function totalProduction(): Attribute
    {
        return Attribute::get(fn () => count($this->productions));
    }

    /**
     * Get the items_count.
     */
    public function totalItem(): Attribute
    {
        $items_count = 0;
        foreach ($this->productions as $production) {
            foreach ($production->items as $item) {
                $items_count += $item->quantity;
            }
        }

        return Attribute::get(fn () => $items_count);
    }

    /**
     * Get the datetime format.
     */
    public function endedAtFormat(): Attribute
    {
        return Attribute::get(fn () => $this->ended_at?->format('h:i A'));
    }

    /**
     * Get the datetime format.
     */
    public function startedAtFormat(): Attribute
    {
        return Attribute::get(fn () => $this->started_at?->format('h:i A'));
    }

    /**
     * Get the totalDuration.
     */
    public function timeDuration(): Attribute
    {
        $ended_at = $this->ended_at ?? now();
        $started_at = $this->started_at;
        $time = gmdate('H:i', $ended_at->diffInSeconds($started_at));
        $time = explode(':', $time);
        $hours = $time[0] > 0 ? $time[0].' Hrs ' : null;
        $minutes = $time[1].' Mins';
        $additional_string = $this->ended_at ? null : ' running';

        return Attribute::get(fn () => $hours.$minutes.$additional_string);
    }

    /**
     * Get the totalDuration.
     */
    public function userName(): Attribute
    {
        return Attribute::get(fn () => CacheUser::find($this->user_id)?->name);
    }
}
