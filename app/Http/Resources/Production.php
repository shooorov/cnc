<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Production extends JsonResource
{
    protected $withoutFields = [];

    public static function collection($resource)
    {
        return tap(new ProductionCollection($resource), function ($collection) {
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
        $items = $this->items;

        return $this->filterFields([
            'id' => $this->id,
			'type' => $this->type,
			'status' => $this->status,
			'quantity' => $this->quantity,
			'duration' => $this->duration,
			'viewed_at' => $this->viewed_at,
			'accepted_at' => $this->accepted_at,
			'completed_at' => $this->completed_at,
			'delivered_at' => $this->delivered_at,
			'order_id' => $this->order_id,
			'branch_id' => $this->branch_id,
			'kitchen_log_id' => $this->kitchen_log_id,

			'order_number' => $this->order_number,
			'order_note' => $this->order_note,
			'stuff_name' => $this->stuff_name,
			'is_viewed' => $this->is_viewed,
			'time_duration' => $this->time_duration,
			'created_at_format' => $this->created_at_format,
			'waiter_alert' => $this->waiter_alert,

			'items' => ProductionItem::collection($items),
        ]);
    }
}
