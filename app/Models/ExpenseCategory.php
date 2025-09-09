<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use SoftDeletes;

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'item_count',
    ];

    // public function itemCount(): Attribute
    // {
    //     $parent_id = $this->id;
    //     $items = Expense::get()->filter(fn ($item) => $item->expense_category_id == $parent_id);

    //     return Attribute::get(fn () => $items->count());
    // }
}
