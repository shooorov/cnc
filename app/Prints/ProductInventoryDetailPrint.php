<?php

namespace App\Prints;

use App\Helpers;
use App\Models\Item;
use App\Models\ProductInventory;
use Illuminate\Contracts\View\View;

class ProductInventoryDetailPrint
{
    private $product_id;

    public function __construct(private $product_inventory_id)
    {
    }

    public function options(): array
    {
        return [
            'orientation' => 'P',
            'default_font_size' => 10,
            'margin_right' => 5,
            'margin_left' => 5,
        ];
    }

    public function view(): View
    {
        $item_name = Helpers::getSettingValueOf('item');
        $product_name = Helpers::getSettingValueOf('product');
        $product_inventory_name = Helpers::getSettingValueOf('product_inventory');
        $product_inventory = ProductInventory::find($this->product_inventory_id);

        $group_items = $required_items = [];
        foreach ($product_inventory->products as $product) {
            foreach ($product->items as $p_item) {
                $group_items[] = $p_item;

                $quantity = $p_item->quantity;
                if ($p_item->inventory_product) {
                    $quantity = $quantity / $p_item->inventory_product->quantity;
                }
                $quantity = round($quantity, 4);

                if (in_array($p_item->item_id, array_keys($required_items))) {
                    $required_items[$p_item->item_id]['quantity'] += $quantity;
                } else {
                    if (! Item::find($p_item->item_id)) {
                        continue;
                    }
                    $required_items[$p_item->item_id] = [
                        'item_name' => $p_item->item?->name,
                        'unit' => $p_item->item?->unit,
                        'quantity' => $quantity,
                    ];
                }
            }
        }

        $params = [
            'heading' => $product_inventory_name.' Detail',
            'item_name' => $item_name,
            'product_name' => $product_name,
            'product_inventory_name' => $product_inventory_name,
            'product_inventory' => $product_inventory,
            'required_items' => $required_items,
        ];

        return view('reports.product-inventory-detail', $params);
    }

    public function headerView(): View
    {
        return view('reports.partials.header');
    }
}
