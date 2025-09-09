<?php

namespace App\Models;

use App\Http\Cache\CacheUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $casts = [
        'permissions' => 'json',
    ];

    /**
     * The accessors to append to the Role's array form.
     *
     * @var array
     */
    protected $appends = [
        'item_count',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the Role Permissions.
     */
    public function itemCount(): Attribute
    {
        $parent_id = $this->id;
        $items = CacheUser::get()->filter(fn ($item) => $item->role_id == $parent_id);

        return Attribute::get(fn () => $items->count());
    }
}
