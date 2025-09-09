<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductItem extends JsonResource
{
    protected $withoutFields = [];

    public static function collection($resource)
    {
        return tap(new ProductItemCollection($resource), function ($collection) {
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
        $unit_use = null;
        $quantity_use = null;
        $unit = $this->item?->unit;
        $quantity = $this->quantity;

        switch ($unit) {
            case 'kg':
                $unit_use = 'g';
                $quantity_use = $quantity * 1000;
                break;
            case 'l':
                $unit_use = 'ml';
                $quantity_use = $quantity * 1000;
                break;
            default:
                $unit_use = 'pcs';
                $quantity_use = $quantity;
                break;
        }

        return $this->filterFields([
            'id' => $this->id,
            'unit' => $unit,
            'quantity' => $quantity,

            'unit_use' => $unit_use,
            'quantity_use' => $quantity_use,

            'item_id' => $this->item_id,
            'product_id' => $this->product_id,
            'in_stock' => $this->item?->in_stock,
            'item_name' => $this->item?->name,
            'avg_rate' => $this->item?->avg_rate,
            'product_name' => $this->product?->name,
        ]);
    }
}
