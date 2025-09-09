<?php

namespace App\Models;

use App\Traits\RemarkTrait;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    use RemarkTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'remark',
    ];

    /**
     * Get the parent remarkable model (Remark).
     */
    public function remarkable()
    {
        return $this->morphTo();
    }
}
