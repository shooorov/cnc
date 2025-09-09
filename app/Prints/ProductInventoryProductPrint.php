<?php

namespace App\Prints;

use App\Helpers;
use App\Http\Cache\CacheProduct;
use App\Models\ProductInventoryProduct;
use Illuminate\Contracts\View\View;

class ProductInventoryProductPrint
{
    private $end_date;

    private $start_date;

    private $direction;

    private $product_id;

    public function __construct($request)
    {
        $this->end_date = $request->end_date;
        $this->start_date = $request->start_date;
        $this->direction = $request->direction;
        $this->product_id = $request->product_id;
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
        $direction = $this->direction;
        $product_id = $this->product_id;

        $end_date = Helpers::dayEndedAt($this->end_date);
        $start_date = Helpers::dayStartedAt($this->start_date);

        $item_inventories = ProductInventoryProduct::latest()
            ->when($start_date, function ($query, $start_date) {
                $query->where('date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->where('date', '<=', $end_date);
            })
            ->when($direction, function ($query, $direction) {
                $query->where('direction', $direction);
            })
            ->when($product_id, function ($query, $product_id) {
                $query->where('product_id', $product_id);
            })
            ->get();

        $product = CacheProduct::find($this->product_id);

        $product_inventory_name = Helpers::getSettingValueOf('product_inventory');

        $params = [
            'heading' => ($product ? $product->name.'\'s ' : '') . ' ' . $product_inventory_name,
            'duration' => 'Duration: '.now()->parse($this->start_date)->format('d/m/Y').' - '.now()->parse($this->end_date)->format('d/m/Y'),
            'item_inventories' => $item_inventories,
        ];

        return view('reports.inventory', $params);
    }

    public function headerView(): View
    {
        return view('reports.partials.header');
    }
}
