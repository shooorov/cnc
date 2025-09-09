<?php

namespace App\Http\Resources;

use App\RolePermission;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDelivery extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'is_complete' => $this->order_delivery?->status == 'delivered',
            'id' => '<div class="text-sm leading-5 text-left"> '.$this->order_delivery?->id.' </div>',
            'SrNo' => '<div class="text-sm leading-5 text-left"> '.$this->order_delivery?->id.' </div>',
            'number' => '<div class="text-sm leading-5 text-left"> '.$this->number.' </div>',
            'invoice_number' => '<div class="text-sm leading-5 text-left"> '.$this->invoice_number.' </div>',
            'address' => '<div class="text-sm leading-5 text-left"> '.$this->order_delivery?->address.' </div>',
            'status' => '<div class="text-sm leading-5 text-left"> '.$this->status().' </div>',
            'datetime_format' => '<div class="text-sm leading-5 text-left"> '.$this->datetime_format.' </div>',
            'rider_name' => '<div class="text-sm leading-5 text-left"> '.$this->order_delivery?->rider?->name.' </div>',
            'customer_name' => '<div class="text-sm leading-5 text-left"> '
                .(($this->order_delivery?->customer?->name != $this->order_delivery?->customer?->mobile) ? ($this->order_delivery?->customer?->name.' <br /> ') : '')
                .$this->order_delivery?->customer?->mobile
                .' </div>',
            'creator_name' => '<div class="text-sm leading-5 text-left"> '.$this->creator_name.' </div>',
            'action' => $this->action(),
        ];
    }

    public function status()
    {
        $classes_array = [
            'pending' => 'bg-yellow-100 text-yellow-900 rounded-full',
            'accept' => 'bg-blue-100 text-blue-900 rounded-full',
            'collect' => 'bg-rose-100 text-rose-900 rounded-full',
            'delivered' => 'bg-green-100 text-green-900 rounded-full',
        ];
        $status = $this->order_delivery?->status;

        return "<div class='inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ".$classes_array[$status]."'>".$status.'</div>';
    }

    public function action()
    {
        $actions = [];

        // if (RolePermission::isRouteValid('delivery.show')) {
        $actions[] = '
			<a href="'.route('delivery.show', $this->id).'" target="_blank" class="text-primary-600 hover:text-primary-800 ml-3" title="detail">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
				</svg>
			</a>';
        // }

        return '<div class="flex justify-end">'.implode('', $actions).'</div>';
    }
}
