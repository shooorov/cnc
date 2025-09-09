<?php

namespace App\Http\Resources;

use App\RolePermission;
use App\UseBranch;
use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
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
            'is_complete' => $this->status == 'complete' || ! empty($this->cash),
            'id' => '<div class="text-sm leading-5 text-center"> '.$this->id.'</div>',
            'branch_name' => '<div class="text-sm leading-5 text-center"> '.$this->branch->name.'</div>',
            'waiter_name' => '<div class="text-sm leading-5 font-normal text-blue-600"> '.$this->waiter_name.'</div>',
            'invoice_number' => '<div class="text-sm leading-5"> '.$this->invoice_number.'</div>',
            'branch_invoice' => '<div class="text-sm leading-5"> <span class="font-semibold">'.$this->branch->name.'</span><br>'.$this->invoice_number.'</div>',
            'datetime_format' => '<div class="text-sm leading-5"> '.$this->datetime_format.'</div>',
            'payment_method_name' => '<div class="text-sm leading-5"> '.$this->payment_method_name.'</div>',
            'discount_amount' => '<div class="text-sm leading-5 text-right"> '.$this->discount_amount.'</div>',
            'discount_type' => '<div class="text-sm leading-5 text-right"> '.$this->discount_type.'</div>',
            'member_code' => '<div class="text-sm leading-5 text-right"> '.$this->member_code.'</div>',
            'member_discount' => '<div class="text-sm leading-5 text-right"> '.$this->member_discount.'</div>',

            'detail' => '<div class="text-sm leading-5"> '.$this->details().'</div>',
            'description' => '<div class="text-sm leading-5"> '.$this->description.'</div>',
            'vat_amount' => '<div class="text-sm leading-5 text-right">'.$this->vat_amount.'</div>',
            'total' => '<div class="text-sm leading-5 text-right">'.$this->total.'</div>',
            'action' => $this->action(),
        ];
    }

    public function details()
    {
        return collect([
            // $this->waiter_name ? '<span class="font-normal text-blue-600">'.$this->waiter_name.'</span>' : '',
            $this->description,
            $this->payment_method_name ? '<span class="font-medium">'.$this->payment_method_name.'</span>' : '',
        ])->filter(fn ($i) => $i)->join('<br />');
    }

    public function action()
    {
        $actions = [];

        if (RolePermission::isRouteValid('pos.print')) {
            $actions[] = '
			<a href="'.route('pos.print', $this->id).'" target="_blank" class="text-gray-600 hover:text-gray-800 ml-2" title="print">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
				</svg>
			</a>';
        }

        if (RolePermission::isRouteValid('order.show')) {
            $actions[] = '
			<a href="'.route('order.show', $this->id).'" class="text-primary-600 hover:text-primary-800 ml-2" title="detail">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
				</svg>
			</a>';
        }

        if (UseBranch::id() && RolePermission::isRouteValid('pos.create')) {
            $actions[] = '
			<a href="'.route('pos.create', $this->id).'" class="text-indigo-600 hover:text-indigo-800 ml-2" title="edit">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
				</svg>
			</a>';
        }

        if (UseBranch::id() && RolePermission::isRouteValid('order.destroy')) {
            $delete_route = route('order.destroy', $this->id);

            $actions[] = '
			<a href="#" class="text-red-600 hover:text-red-800 ml-2" title="delete" id="data-route-'.$this->id.'" data-route="'.$delete_route.'" onclick="event.preventDefault();if(confirm(\'Do you really want to delete this result\')){document.getElementById(\'delete-form\').setAttribute(\'action\', document.querySelector(\'#data-route-'.$this->id.'\').dataset.route); document.getElementById(\'delete-form\').submit();}">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
				</svg>
			</a>';
        }

        return '<div class="flex justify-end">'.implode('', $actions).'</div>';
    }
}
