<?php

namespace App\Prints;

use App\Models\Employee;
use App\Models\OrderProductEmployee;
use Illuminate\Contracts\View\View;

class EmployeePrint
{
    public function __construct(private $start_date, private $end_date, private $employee_id)
    {
    }

    public function options(): array
    {
        return [
            'orientation' => 'P',
        ];
    }

    public function view(): View
    {
        $employee_id = $this->employee_id;

        $end_date = $this->end_date ? now()->parse($this->end_date) : now();
        $start_date = $this->start_date ? now()->parse($this->start_date) : now()->subMonth();
        $date_duration = $end_date == $start_date ? $start_date->format('d/m/Y') : ($start_date->format('d/m/Y').' - '.$end_date->format('d/m/Y'));

        $employees = Employee::when($employee_id, function ($query, $employee_id) {
            $query->where('id', $employee_id);
        })->get();

        foreach ($employees as $employee) {
            $employee->services = OrderProductEmployee::where('employee_id', $employee->id)->when($start_date, function ($query, $start_date) {
                $query->whereDate('created_at', '>=', $start_date);
            })
                ->when($end_date, function ($query, $end_date) {
                    $query->whereDate('created_at', '<=', $end_date);
                })
                ->get();
        }

        $params = [
            'heading' => 'Employee Sale Status',
            'duration' => $date_duration,
            'employees' => $employees,
        ];

        return view('reports.employee', $params);
    }

    public function headerView(): View
    {
        return view('reports.partials.header', ['options' => $this->options()]);
    }
}
