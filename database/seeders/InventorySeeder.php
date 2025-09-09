<?php

namespace Database\Seeders;

use App\Http\Cache\CacheItem;
use App\Models\ItemInventory;
use App\Models\ItemInventoryItem;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item_ids = array_column(CacheItem::get(), 'id');

        $items = [];
        $sum_total = 0;
        for ($i = 0; $i < 5; $i++) {
            $qty = rand(3, 8);
            $rate = rand(3, 30) * 10;
            $sum_total += ($qty * $rate);
            $item = [
                'item_id' => $item_ids[array_rand($item_ids)],
                'quantity' => $qty,
                'rate' => $rate,
                'total' => $rate * $qty,
            ];
            $items[] = $item;
        }
        $serial = ItemInventory::count() + 1;
        $item_inventory = ItemInventory::create([
            'date' => now(),
            'serial' => $serial,
            'direction' => 'in',
            'total' => $sum_total,
        ]);

        foreach ($items as $key => $item) {
            ItemInventoryItem::create([
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'total' => $item['total'],
                'item_inventory_id' => $item_inventory->id,
            ]);
        }
    }
}
