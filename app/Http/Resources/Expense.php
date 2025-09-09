<?php

namespace App\Http\Resources;

use App\RolePermission;
use Illuminate\Http\Resources\Json\JsonResource;

class Expense extends JsonResource
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
            'id' => '<div class="text-sm leading-5 text-center"> '.$this->id.'</div>',
            'branch_name' => '<div class="text-sm leading-5"> '.$this->branch->name.'</div>',
            'account_name' => '<div class="text-sm leading-5 font-normal"> '.$this->account_name.'</div>',
            'category_name' => '<div class="text-sm leading-5"> '.$this->category_name.'</div>',
            'datetime_format' => '<div class="text-sm leading-5"> '.$this->datetime_format.'</div>',
            'created_at_format' => '<div class="text-sm leading-5 text-right"> '.$this->created_at_format.'</div>',
            'amount' => '<div class="text-sm leading-5 text-right"> '.$this->amount.'</div>',
            'detail' => '<div class="text-sm leading-5"> '.$this->remark.'</div>',
            'description' => '<div class="text-sm leading-5"> '.$this->description.'</div>',
            'status' => '<div class="text-sm leading-5 text-center"> '.$this->status().' </div>',
            'action' => $this->action(),
        ];
    }

    public function status()
    {
        $classes_array = [
            'unpaid' => 'bg-yellow-200 text-yellow-800 rounded',
            'paid' => 'bg-green-200 text-green-800 rounded',
        ];
        $status = $this->status;

        return "<div class='inline-flex px-2 py-1 text-xs font-medium ".$classes_array[$status]."'>".ucfirst($status).'</div>';
    }

    public function action()
    {
        $actions = [];

        if (RolePermission::isRouteValid('expense.show')) {
            $actions[] = '
			<a href="'.route('expense.show', $this->id).'" class="text-primary-600 hover:text-primary-800 ml-2" title="detail">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
				</svg>
			</a>';
        }

        if (RolePermission::isRouteValid('expense.edit')) {
            $actions[] = '
			<a href="'.route('expense.edit', $this->id).'" class="text-indigo-600 hover:text-indigo-800 ml-2" title="edit">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
				</svg>
			</a>';
        }

        if (RolePermission::isRouteValid('expense.destroy')) {
            $delete_route = route('expense.destroy', $this->id);

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
