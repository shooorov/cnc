<?php

namespace App\Prints;

use Illuminate\Contracts\View\View;

class KitchenDeliveryPrint
{
    private $kitchen_delivery;

    public function __construct($kitchen_delivery)
    {
        $this->kitchen_delivery = $kitchen_delivery;
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
        $params = [
            'heading' => 'Kitchen Delivery '.$this->kitchen_delivery->branch?->name.' - '.$this->kitchen_delivery->date->format('Y-m-d'),
            'kitchen_delivery' => $this->kitchen_delivery,
        ];

        return view('reports.kitchen_delivery', $params);
    }

    public function headerView(): View
    {
        return view('reports.partials.header');
    }
}
