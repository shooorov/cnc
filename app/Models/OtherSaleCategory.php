<?php

namespace App\Models;

use App\Http\Cache\CacheOtherSale;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherSaleCategory extends Model
{
    use SoftDeletes;

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'item_count',
    ];

    public function itemCount(): Attribute
    {
        $parent_id = $this->id;
        $items = CacheOtherSale::get()->filter(fn ($item) => $item->other_sale_category_id == $parent_id);

        return Attribute::get(fn () => $items->count());
    }
}
