<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KitchenDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_date' => ['required', 'date'],
            'requisition_id' => ['required', 'exists:product_requisitions,id'],
            'central_kitchen_id' => ['required', 'exists:central_kitchens,id'],
            'total' => ['required', 'numeric'],

            'group_items.*.product_id' => ['required', 'exists:products,id'],
            'group_items.*.delivery_quantity' => ['nullable', 'numeric'],
            'group_items.*.rate' => [
                'nullable',
                'numeric',
                function ($attribute, $value, $fail) {
                    $this->validateRateWithName($attribute, $value, $fail);
                },
            ],
            'group_items.*.avg_rate' => ['nullable', 'numeric'],
            'group_items.*.delivery_total' => ['nullable', 'numeric'],
        ];
    }

    /**
     * Validate that rate is greater than 0 if delivery_quantity > 0
     * Show validation error with product name instead of index
     */
    protected function validateRateWithName($attribute, $value, $fail)
    {
        preg_match('/group_items\.(\d+)\.rate/', $attribute, $matches);
        $index = $matches[1] ?? null;

        if ($index !== null) {
            $data = request()->all();
            $deliveryQuantity = $data['group_items'][$index]['delivery_quantity'] ?? 0;
            $productName = $data['group_items'][$index]['name'] ?? "Item #$index";

            if ($deliveryQuantity > 0 && (!$value || $value == 0)) {
                $fail("The rate for product '{$productName}' must be greater than 0 because delivery quantity is more than 0.");
            }
        }
    }
}
