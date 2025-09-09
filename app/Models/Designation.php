<?php

namespace App\Models;

use App\Http\Cache\CacheEmployee;
use App\Http\Cache\CacheRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use SoftDeletes;

    protected $appends = [
        'in_role',
        'item_count',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function itemCount(): Attribute
    {
        $designation_id = $this->id;
        $items = CacheEmployee::get()->filter(fn ($item) => $item->designation_id == $designation_id);

        return Attribute::get(fn () => $items->count());
    }

    public function inRole(): Attribute
    {
        $roles = CacheRole::get()->map(fn ($role) => strtolower($role->name))->toArray();

        return Attribute::get(fn () => in_array(strtolower($this->name), $roles));
    }
}
