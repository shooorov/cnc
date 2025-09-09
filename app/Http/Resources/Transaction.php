<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Transaction extends JsonResource
{
    protected $withoutFields = [];

    public static function collection($resource)
    {
        return tap(new TransactionCollection($resource), function ($collection) {
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
        return $this->filterFields([
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d'),
            'check_no' => $this->check_no,
            'direction' => ucfirst($this->direction),
            'amount' => $this->amount,
            'remark' => $this->remark,

            'account_id' => $this->account_id,
            'creator_id' => $this->creator_id,

            'account_name' => $this->account_name,
            'creator' => $this->creator_name,
            'modifier' => 'This is mystery',
        ]);
    }
}
