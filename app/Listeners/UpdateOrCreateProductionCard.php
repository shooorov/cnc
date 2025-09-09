<?php

namespace App\Listeners;

use App\Events\OrderModified;
use App\Http\Cache\CacheProduction;
use App\Http\Cache\CacheProductionItem;
use App\Models\Order;
use App\Models\Production;
use App\Models\ProductionItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class UpdateOrCreateProductionCard implements ShouldQueue
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
    public function handle(OrderModified $event)
    {
        if (! config('module.production')) {
            return;
        }
        $order = Order::find($event->order['id']);

        if ($order->status == 'complete') {
            foreach ($order->productions as $production) {
                $production->update([
                    'status' => 'complete',
                ]);
            }
        }

        $productions = (new CreateProductionCard())->getProductionData($order);
        $prev_production_products = [];

        foreach ($productions as $producer => $items) {

            $production = Production::firstOrCreate([
                'type' => $producer,
                'order_id' => $order->id,
                'branch_id' => $order->branch_id,
                'status' => 'pending',
            ]);

            if ($production->status == 'pending') {
                ProductionItem::where('production_id', $production->id)->delete();
            }

            $processing_production_ids = Production::where('type', $producer)
                ->where('order_id', $order->id)
                ->where('branch_id', $order->branch_id)
                ->where('status', '!=', 'pending')
                ->pluck('id');

            $processing_items = ProductionItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
                ->whereIn('production_id', $processing_production_ids)
                ->groupBy('product_id')
                ->get();

            // if there is a need for another production card then check all old production card items
            foreach ($processing_items as $processing_item) {
                $already_quantity = $processing_item->total_quantity;

                $item = collect($items)->first(fn ($item) => $item->product_id == $processing_item->product_id);

                $final_quantity = $item?->quantity ?? 0;
                $change_quantity = $final_quantity - $already_quantity;

                ProductionItem::updateOrCreate([
                    'product_id' => $processing_item->product_id,
                    'production_id' => $production->id,
                    'status' => 'pending',
                ], [
                    'quantity' => $change_quantity,
                ]);

                array_push($prev_production_products, $processing_item->product_id);
            }

            // Only takes new item entry
            foreach ($items as $item) {
                if (in_array($item->product_id, $prev_production_products)) {
                    continue;
                }

                ProductionItem::updateOrCreate([
                    'product_id' => $item->product_id,
                    'production_id' => $production->id,
                    'status' => 'pending',
                ], [
                    'quantity' => $item->quantity,
                ]);
            }

            $production_quantity = ProductionItem::where('production_id', $production->id)->sum('quantity');
            $production_items = ProductionItem::where('production_id', $production->id)->get()->filter(fn ($item) => $item->quantity != 0)->count();

            if ($production_items > 0) {
                $production->update(['quantity' => $production_quantity]);
            } else {
                $production->delete();
            }
        }

        CacheProduction::forget();
        CacheProductionItem::forget();
    }
}
