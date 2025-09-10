<?php

namespace App\Http\Resources;

use App\Http\Cache\CachePlatter;
use App\Http\Cache\CacheProductItem;
use App\Models\Product as ModelsProduct;
use App\Models\ProductItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;


class Product extends JsonResource
{
    protected $withoutFields = [];

    public static function collection($resource)
    {
        return tap(new ProductCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
    }



    // Set the keys that are supposed to be filtered out
    public function hide(array $fields)
    {
        $this->withoutFields = $fields;

        return $this;
    }

    // Remove the filtered keys.
    protected function filterFields($array)
    {
        return collect($array)->forget($this->withoutFields)->toArray();
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $group_items = CacheProductItem::get()->where('product_id', $this->id)->values();
        $platter_items = CachePlatter::get()->where('product_id', $this->id)->values();
        //$remaining = $this->items->where('direction', 'in')->sum('quantity') - $this->inventories->where('direction', 'out')->sum('quantity');

        $remaining = $this->inventories->where('direction', 'in')->sum('quantity') - $this->inventories->where('direction', 'out')->sum('quantity');
        // dd($this->items,$request,$remaining);
        // die();
        //$group_items = ProductItem::get()->where('product_id', $this->id)->values();
        // dd($this->image_url);

        return $this->filterFields([
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'english_name' => $this->english_name,
            'vat_applicable' => $this->vat_applicable,
            'title' => $this->name . ' - ' . $this->code,
            'rate' => $this->rate,
            'discount' => $this->discount,
            'number_of_persons' => $this->number_of_persons,
            'status' => $this->status,
            'margin_amount' => $this->margin_amount,
            'margin_percent' => $this->margin_percent,
            'description' => $this->description,
            'is_platter' => $this->is_platter,

            //'group_items' => count($group_items) ? ProductionItem::collection($group_items) : [],
            'group_items' => count($group_items) ? new ResourceCollection($group_items) : [],

            'items_count' => (count($this->items) ?? 0) . (count($this->items) > 1 ? ' Items ' : ' Item'),
            'platter_items' => $platter_items,

            'remaining' => $remaining,
            'old_remaining' => $remaining,

            'product_category_id' => $this->product_category_id,
            'category_name' => $this->product_category?->name,

            'image' => $this->image_url ?? asset('img/product.jpg'),
            'image_default' => $this->default_image_url ?? asset('img/product.jpg'),

            'subTitle' => $this->name . ' - ' . $this->code,
            'price' => $this->rate,
            'images' => [
                [
                    'url' => $this->image ?? asset('img/product.jpg'),
                    'thumbnailUrl' => $this->image ?? asset('img/product.jpg'),
                ],
            ],
        ]);
    }
}
