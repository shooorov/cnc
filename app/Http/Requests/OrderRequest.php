<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $dine_status = $this->dine_type == 'dine-in' ? 'required' : 'nullable';

        return [
            'customer_name' => ['nullable', 'string', 'max:191'],
            'customer_mobile' => ['nullable', 'string'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'sub_total' => ['nullable', 'numeric'],
            'dine_type' => ['required', 'string'],
            'guest_number' => [$dine_status, 'numeric'],
            'discount_type' => ['nullable', 'string'],
            'discount_rate' => ['nullable', 'numeric'],
            'discount_amount' => ['nullable', 'numeric'],
            'service_cost' => ['nullable', 'numeric'],
            'vatable_amount' => ['nullable', 'numeric'],
            'vat_rate' => ['nullable', 'numeric'],
            'vat_amount' => ['nullable', 'numeric'],
            'total' => ['nullable', 'numeric'],
            'token' => ['nullable', 'numeric'],
            'cash' => ['nullable', 'numeric'],
            'change' => ['nullable', 'numeric'],
            'waiter_id' => ['nullable', 'exists:users,id'],
            'rider_id' => ['nullable', 'exists:users,id'],
            'table' => ['nullable', 'exists:stuffs,id'],
            'status' => ['required', 'string'],
            'print' => ['nullable', 'boolean'],
            'date_time' => ['nullable', 'string'],
            'group_items' => ['required', 'array'],
        ];
    }

    public function messages()
    {
        return [
            'group_items.required' => 'Order menu item not found!',
        ];
    }
}
