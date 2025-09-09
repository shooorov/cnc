<?php

namespace App\Exports;

use App\Helpers;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExpenseExport implements FromView
{
    public function __construct(private $start_date, private $end_date, private $account_id)
    {
    }

    public function view(): View
    {
        $account_id = $this->account_id;

        $end_date = $this->end_date ? Helpers::dayEndedAt($this->end_date) : Helpers::dayEndedAt(now());
        $start_date = $this->start_date ? Helpers::dayStartedAt($this->start_date) : Helpers::dayStartedAt(now()->subMonth());

        $items = Expense::when($start_date, function ($query, $start_date) {
            $query->where('date', '>=', $start_date);
        })
            ->when($end_date, function ($query, $end_date) {
                $query->where('date', '<=', $end_date);
            })
            ->when($account_id, function ($query, $account_id) {
                $query->where('account_id', $account_id);
            })
            ->get();

        $accounts = Account::pluck('name', 'id');
        $categories = ExpenseCategory::pluck('name', 'id');

        return view('exports.expenses', [
            'items' => $items,
            'accounts' => $accounts,
            'categories' => $categories,
        ]);
    }
}
