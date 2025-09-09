<?php

namespace App\Http\Controllers;

use App\Exports\ExpenseExport;
use App\Exports\OrderExport;
use App\Exports\SaleExport;
use App\Exports\SaleItemExport;
use App\Exports\SaleProductExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function sale(Request $request)
    {
        $isSuperAdmin = $request->user()->is_admin;

        $start_date = now()->parse($request->start_date);
        $end_date = now()->parse($request->end_date);
        $file_name = "Sales {$start_date->format('Y-m-d')}_{$end_date->format('Y-m-d')}.xlsx";

        // ob_clean();
       
        return Excel::download(new SaleExport($isSuperAdmin,$start_date, $end_date), $file_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function saleProduct(Request $request)
    {
        $start_date = now()->parse($request->start_date);
        $end_date = now()->parse($request->end_date);
        $file_name = "Sales products {$start_date->format('Y-m-d')}_{$end_date->format('Y-m-d')}.xlsx";

        // ob_clean();
        return Excel::download(new SaleProductExport($start_date, $end_date, $request->category_id), $file_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function saleItem(Request $request)
    {
        $start_date = now()->parse($request->start_date);
        $end_date = now()->parse($request->end_date);
        $file_name = "Sales items {$start_date->format('Y-m-d')}_{$end_date->format('Y-m-d')}.xlsx";

        // ob_clean();
        return Excel::download(new SaleItemExport($start_date, $end_date, $request->category_id), $file_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function order(Request $request)
    {
        $start_date = now()->parse($request->start_date);
        $end_date = now()->parse($request->end_date);
        $file_name = "Order {$start_date->format('Y-m-d')}_{$end_date->format('Y-m-d')}.xlsx";

        // ob_clean();
        return Excel::download(new OrderExport(
            $request->start_date,
            $request->end_date,
            $request->payment_method_id,
            $request->customer_id,
            $request->creator_id
        ), $file_name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function expense(Request $request)
    {
        $start_date = now()->parse($request->start_date);
        $end_date = now()->parse($request->end_date);
        $file_name = "Expenses {$start_date->format('Y-m-d')}_{$end_date->format('Y-m-d')}.xlsx";

        // ob_clean();
        return Excel::download(new ExpenseExport($start_date, $end_date, $request->account_id), $file_name, \Maatwebsite\Excel\Excel::XLSX);
    }
}
