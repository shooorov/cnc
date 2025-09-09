<?php

namespace App;

class Chart
{
    public function __construct()
    {
    }

    public static function line($value, $start_date, $end_date)
    {
        $current = clone $start_date;

        $data = [];
        do {
            $time = $current->format('h:i A');

            $data[] = [
                'dateFormat' => 'yyyy-MM-dd H:i',
                'xAxisName' => 'Hourly',
                'yAxisName' => 'Sales',
                'xAxisValue' => $current->format('Y-m-d h:i A'),
                'xAxisTooltip' => $current->format('H A'),
                'yAxisValue' => floatval($value[$time] ?? 0),
            ];

            $current = $current->addHour();

        } while ($current->lt($end_date));

        return $data;
    }

    public static function column($value, $start_date, $end_date, $duration = 'day')
    {
        $current = clone $start_date;

        if ($duration == 'month') {
            $format = 'M, y';
        } else {
            $format = 'd M, y';
        }

        $data = [];
        do {
            $time = $current->format($format);

            $data[] = [
                'dateFormat' => 'yyyy-MM-dd',
                'xAxisName' => 'Daily',
                'yAxisName' => 'Sales',
                'xAxisValue' => $current->format($format),
                'xAxisTooltip' => $current->format($format),
                'yAxisValue' => floatval($value[$time] ?? 0),
            ];

            if ($duration == 'month') {
                $current = $current->addMonth();
            } else {
                $current = $current->addDay();
            }

        } while ($current->lt($end_date));

        return $data;
    }
}
