<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Http\Cache\CacheProduction;
use App\Http\Cache\CacheProductionItem;
use App\Models\Order;
use App\Models\Production;
use App\Models\ProductionItem;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateProductionCard implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(OrderPlaced $event)
    {
        if (! config('module.production')) {
            return;
        }
        $order = Order::find($event->order['id']);

        $productions = $this->getProductionData($order);

        foreach ($productions as $producer => $items) {
            $production = Production::create([
                'type' => $producer,
                'order_id' => $order->id,
                'branch_id' => $order->branch_id,
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                ProductionItem::create([
                    'product_id' => $item->product_id,
                    'production_id' => $production->id,
                    'status' => 'pending',
                    'quantity' => $item->quantity,
                ]);
            }

            $production_quantity = ProductionItem::where('production_id', $production->id)->sum('quantity');
            $production->update(['quantity' => $production_quantity]);
        }

        CacheProduction::forget();
        CacheProductionItem::forget();
    }

    public function getProductionData(Order $order)
    {
        $productions = [];
        foreach ($order->products as $item) {
            $category = $item->product?->product_category;

            if ($item->product?->is_platter) {
                $platter_items = $item->product->platters;

                foreach ($platter_items as $platter_item) {
                    $category = $platter_item->product->product_category;

                    $platter_item->product_id = $platter_item->item_id;
                    $platter_item->quantity = $platter_item->quantity * $item->quantity;
                    if ($category?->type != null) {
                        $productions[$category->type][] = $platter_item;
                    }
                }
            } elseif ($category?->type != null) {
                $productions[$category->type][] = $item;
            }
        }

        return $productions;
    }
}
