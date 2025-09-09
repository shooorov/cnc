<?php

namespace App\Prints;

use App\Helpers;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class SaleVatPrint
{
    public function __construct(private $start_date, private $end_date)
    {
    }

    public function options(): array
    {
        return [
            'orientation' => 'P',
            'default_font_size' => 10,
        ];
    }

    public function view(): View
    {
        $end_date = Helpers::dayEndedAt($this->end_date);
        $start_date = Helpers::dayStartedAt($this->start_date);
        $date_duration = Helpers::operationDay($end_date) == Helpers::operationDay($start_date) ? $start_date->format('d/m/Y') : ($start_date->format('d/m/Y').' - '.Helpers::dayStartedAt(Helpers::operationDay($end_date))->format('d/m/Y'));

        $durations = [];

        $diff = $start_date->diffInDays($end_date);
        for ($i = 0; $i <= $diff; $i++) {

            $end_date_day = Helpers::dayEndedAt($start_date);
            $start_date_day = Helpers::dayStartedAt($start_date);

            $data = Order::where('date', '>=', $start_date_day)->where('date', '<=', $end_date_day);

            $durations[] = [
                'date' => $start_date->format('d/m/Y'),
                'vat' => $data->sum('vat_amount'),
                'total' => $data->sum('total'),
                'sub_total' => $data->sum('sub_total'),
                'discount_amount' => $data->sum('discount_amount'),
            ];
            $start_date->addDay();
        }

        $params = [
            'heading' => 'VAT Report',
            'bin_number' => Helpers::getSettingValueOf('bin_number'),
            'durations' => $durations,
            'date_duration' => $date_duration,
        ];

        return view('reports.vat', $params);
    }

    public function headerView(): View
    {
        return view('reports.partials.header', ['options' => $this->options()]);
    }
}
