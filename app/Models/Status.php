<?php

namespace App\Models;

use App\Traits\StatusTrait;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use StatusTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'previous_status',
        'changed_status',
        'user_id',
    ];

    /**
     * Get the parent statusable model (Status).
     */
    public function statusable()
    {
        return $this->morphTo();
    }
}
