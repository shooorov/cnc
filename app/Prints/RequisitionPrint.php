<?php

namespace App\Prints;

use Illuminate\Contracts\View\View;

class RequisitionPrint
{
    private $requisition;

    public function __construct($requisition)
    {
        $this->requisition = $requisition;
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
            'heading' => 'Requisition '.$this->requisition->date->format('Y-m-d'),
            'requisition' => $this->requisition,
        ];

        return view('reports.requisition', $params);
    }

    public function headerView(): View
    {
        return view('reports.partials.header');
    }
}
