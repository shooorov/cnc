<?php

namespace App\Prints;

use App\Helpers;
use App\Http\Cache\CacheItem;
use App\Models\ItemInventory;
use Illuminate\Contracts\View\View;

class ItemInventoryItemPrint
{
    private $end_date;

    private $start_date;

    private $item_id;

    public function __construct($request)
    {
        $this->end_date = $request->end_date;
        $this->start_date = $request->start_date;
        $this->item_id = $request->item_id;
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
        $item_id = $this->item_id;
        $end_date = Helpers::dayEndedAt($this->end_date);
        $start_date = Helpers::dayStartedAt($this->start_date);

        $item_inventories = ItemInventory::when($start_date, function ($query, $start_date) {
            $query->where('date', '>=', $start_date);
        })
            ->when($end_date, function ($query, $end_date) {
                $query->where('date', '<=', $end_date);
            })
            ->when($item_id, function ($query, $item_id) {
                $query->where('item_id', $item_id);
            })
            ->latest()
            ->get();

        $item_inventory_name = Helpers::getSettingValueOf('item_inventory');
        $item = CacheItem::find($this->item_id);

        $params = [
            'heading' => ($item ? $item->name.'\'s ' : '') . ' ' . $item_inventory_name,
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
