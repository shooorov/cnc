<?php

namespace App\Models;

use App\Models\Scopes\UseBranchScope;
use App\Traits\StatusTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use StatusTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected $fillable = [
        'type',
        'status',
        'quantity',
        'duration',
        'viewed_at',
        'accepted_at',
        'completed_at',
        'delivered_at',
        'order_id',
        'branch_id',
        'kitchen_log_id',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'order_number',
        'order_note',
        'stuff_name',
        'is_viewed',
        'time_duration',
        'created_at_format',
        'waiter_alert',
    ];

    public $statuses = [
        'pending' => 'Pending',
        'accept' => 'Cooking',
        'complete' => 'Done',
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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(ProductionItem::class);
    }

    public function kitchen_log()
    {
        return $this->belongsTo(KitchenLog::class);
    }

    /**
     * Get the order_number.
     */
    public function orderNumber(): Attribute
    {
        return Attribute::get(fn () => $this->order?->invoice_number);
    }

    /**
     * Get the is_viewed.
     */
    public function isViewed(): Attribute
    {
        return Attribute::get(fn () => $this->viewed_at ? true : false);
    }

    /**
     * Get the order_note.
     */
    public function orderNote(): Attribute
    {
        return Attribute::get(fn () => $this->order?->note);
    }

    /**
     * Get the stuff_name.
     */
    public function stuffName(): Attribute
    {
        return Attribute::get(fn () => $this->order?->stuff_name);
    }

    /**
     * Get the created_at_raw.
     */
    public function createdAtRaw(): Attribute
    {
        return Attribute::get(fn () => $this->created_at ? $this->created_at->format('Y-m-d\TH:i') : null);
    }

    /**
     * Get the created_at_format.
     */
    public function createdAtFormat(): Attribute
    {
        return Attribute::get(fn () => $this->created_at ? $this->created_at->format('h:i A') : null);
    }

    /**
     * Get the accepted_at_raw.
     */
    public function acceptedAtRaw(): Attribute
    {
        return Attribute::get(fn () => $this->accepted_at ? $this->accepted_at->format('Y-m-d\TH:i') : null);
    }

    /**
     * Get the completed_at_raw.
     */
    public function completedAtRaw(): Attribute
    {
        return Attribute::get(fn () => $this->completed_at ? $this->completed_at->format('Y-m-d\TH:i') : null);
    }

    /**
     * Get the delivered_at_raw.
     */
    public function deliveredAtRaw(): Attribute
    {
        return Attribute::get(fn () => $this->delivered_at ? $this->delivered_at->format('Y-m-d\TH:i') : null);
    }

    /**
     * Get the total_duration.
     */
    public function timeDuration(): Attribute
    {
        $accepted_at = $this->accepted_at ?? now();
        $completed_at = $this->completed_at;
        $time = gmdate('H:i', $accepted_at->diffInSeconds($completed_at));
        $time = explode(':', $time);
        $hours = $time[0] > 0 ? $time[0].' Hrs ' : null;
        $minutes = $time[1].' Mins';
        $additional_string = $this->accepted_at ? null : ' running';

        return Attribute::get(fn () => $hours.$minutes.$additional_string);
    }

    /**
     * Get the waiter_alert.
     */
    public function waiterAlert(): Attribute
    {
        return Attribute::get(fn () => $this->status == 'complete' && ! $this->delivered_at);
    }
}
