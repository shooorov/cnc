<?php

namespace App\Exports;

use App\Helpers;
use App\Models\Item;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class SaleItemExport implements FromView
{
    public function __construct(private $start_date, private $end_date, private $category_id)
    {
    }

    public function view(): View
    {
        $category_id = $this->category_id;

        $end_date = Helpers::dayEndedAt($this->end_date);
        $start_date = Helpers::dayStartedAt($this->start_date);

        $sold_products = DB::table('orders')->select(
            'product_id',
            'products.name',
            'products.rate',
            DB::raw('SUM(order_products.quantity) as product_quantity'),
            DB::raw('SUM(order_products.quantity * order_products.rate) as product_amount')
            // DB::raw('FORMAT(SUM(order_products.quantity), 0) as product_quantity'),
            // DB::raw('FORMAT(SUM(order_products.quantity * order_products.rate), 2) as product_amount')
        )
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->join('product_categories', 'product_categories.id', '=', 'products.product_category_id')
            ->where('orders.created_at', '>=', $start_date)
            ->where('orders.created_at', '<=', $end_date)
            ->when($category_id, function ($query, $category_id) {
                $query->where('products.product_category_id', $category_id);
            })
            ->whereNull('orders.deleted_at')
            ->groupBy('product_id', 'products.name', 'products.rate')
            ->orderBy('products.name')
            ->get();

        $items = Item::all();

        foreach ($sold_products as $sold_product) {
            $product = Product::find($sold_product->product_id);

            foreach ($product->items as $product_item) {
                $item = $items->first(fn ($i) => $i->id == $product_item->item_id);
                $item->quantity = ($item->quantity ?? 0) + $product_item->quantity;
            }
        }

        return view('exports.sale-items', [
            'items' => $items->filter(fn ($i) => $i->quantity > 0),
            'filter' => [
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ]);
    }
}
