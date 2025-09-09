<?php

namespace App\Prints;

use App\Helpers;
use Illuminate\Contracts\View\View;

class InvoicePrint
{
    public function __construct(private $order)
    {
    }

    public function options(): array
    {
        return [
            'format' => [80, 297],
            'orientation' => 'P',
            'default_font_size' => 8,
            'margin_top' => 5,
            'margin_right' => 5,
            'margin_footer' => 5,
            'margin_left' => 5,
            'default_font' => 'mono',
        ];
    }

    public function view(): View
    {
        $params = [
            'order' => $this->order,
            'company_name' => Helpers::getSettingValueOf('company_name'),
            'company_address' => Helpers::getSettingValueOf('company_address'),
            'opening_hours' => $this->order->branch?->opening_hours,
        ];

        return view('reports.invoice', $params);
    }
}
