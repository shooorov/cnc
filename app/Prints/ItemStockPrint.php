<?php

namespace App\Prints;

use App\Helpers;
use App\UseStock;
use Illuminate\Contracts\View\View;

class ItemStockPrint
{
    public function __construct()
    {
    }

    public function options(): array
    {
        return [
            'orientation' => 'P',
            'default_font_size' => 10,
            'margin_right' => 8,
            'margin_left' => 8,
        ];
    }

    public function view(): View
    {
        $item_name = Helpers::getSettingValueOf('item');

        $params = [
            'heading' => $item_name.' Stock '.now()->format('d/m/Y h:i A'),
            'items' => UseStock::itemsStock(),
        ];

        return view('reports.stock', $params);
    }

    public function headerView(): View
    {
        return view('reports.partials.header');
    }
}
