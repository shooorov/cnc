<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
{
    protected $withoutFields = [];

    public static function collection($resource)
    {
        return tap(new ItemCollection($resource), function ($collection) {
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
        $unit = $this->unit;
        switch ($unit) {
            case 'kg':
                $unit_use = 'g';
                break;
            case 'l':
                $unit_use = 'ml';
                break;
            default:
                $unit_use = 'pcs';
                break;
        }

        return $this->filterFields([
            'id' => $this->id,
            'name' => $this->name,
            'avg_rate' => floatval($this->avg_rate),
            'min_limit' => floatval($this->min_limit),
            'unit' => $unit,
            'unit_use' => $unit_use,
            // 'in_stock'          => $this->in_stock,

            'item_category_id' => $this->item_category_id,
            // 'category_name'     => $this->item_category?->name,

            // 'image'             => $this->image ?? asset('img/item.jpg'),
            'image_default' => asset('img/item.jpg'),
        ]);
    }
}
