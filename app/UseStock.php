<?php

namespace App;

use App\Models\Item;
use App\Models\ItemInventoryItem;
use App\Models\Product;
use App\Models\ProductInventoryProduct;

class UseStock
{
    public static function itemsStock()
    {
        $items_list = ItemInventoryItem::select('item_id')->orderBy('item_id', 'ASC')->distinct()->pluck('item_id')->toArray();
        $items = Item::all()->filter(fn ($item) => in_array($item->id, $items_list));
        $items_array = [];

        foreach ($items as $item) {
            $unit = $item?->unit ?? 'pcs';
            $quantity = $item->inventories->where('direction', 'in')->sum('quantity') - $item->inventories->where('direction', 'out')->sum('quantity');
            $items_array[] = [
                'id' => $item->id,
                'unit' => $unit,
                'name' => $item->name,
                'min_limit' => $item->min_limit,
                'avg_rate' => floatval($item->avg_rate),
                'quantity' => floatval(round($quantity, 3)),
            ];
        }

        return $items_array;
    }

    public static function productsStock()
    {
        $products_list = ProductInventoryProduct::select('product_id')->orderBy('product_id', 'ASC')->distinct()->pluck('product_id')->toArray();
        $products = Product::all()->filter(fn ($item) => in_array($item->id, $products_list));
        $products_array = [];
        foreach ($products as $product) {
            $quantity = $product->inventories->where('direction', 'in')->sum('quantity') - $product->inventories->where('direction', 'out')->sum('quantity');
            $products_array[] = [
                'id' => $product->id,
                'name' => $product->name,
                'unit' => 'pcs',
                'rate' => floatval($product->rate),
                'cost' => floatval($product->cost),
                'quantity' => floatval($quantity),
                'production_cost' => floatval($product->production_cost),
            ];
        }

        return $products_array;
    }
}
