<?php

namespace App\Http\Controllers\Operation;

use App\Http\Cache\CacheAccount;
use App\Http\Cache\CacheOtherSale;
use App\Http\Cache\CacheOtherSaleCategory;
use App\Http\Cache\CacheTransaction;
use App\Http\Controllers\Controller;
use App\Models\OtherSale;
use App\Models\Transaction;
use App\RolePermission;
use App\UseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OtherSaleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the other_sales list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $isDateSearch = RolePermission::isEnabled('record_search.other_sale_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? now()->parse($request->end_date) : now();
            $start_date = $request->start_date ? now()->parse($request->start_date) : now()->startOfMonth();
        } else {
            $end_date = now();
            $start_date = now()->startOfMonth();
        }

        $account_id = $request->account_id;
        $branch_id = $request->branch_id;
        $other_sale_category_id = $request->other_sale_category_id;

        $other_sales = CacheOtherSale::get()
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date)
            ->when($account_id, function ($transactions, $account_id) {
                return $transactions->where('account_id', $account_id);
            })->when($branch_id, function ($transactions, $branch_id) {
                return $transactions->where('branch_id', $branch_id);
            })->when($other_sale_category_id, function ($transactions, $other_sale_category_id) {
                return $transactions->where('other_sale_category_id', $other_sale_category_id);
            });

        $params = [
            'other_sales' => $other_sales,
            'categories' => CacheOtherSaleCategory::get(),
            'accounts' => CacheAccount::get(),
            'filter' => [
                'account_id' => $account_id,
                'branch_id' => $branch_id,
                'other_sale_category_id' => $other_sale_category_id,
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/OtherSale/Index', $params);
    }

    /**
     * Show the other_sale create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        $params = [
            'date' => now()->format('Y-m-d\TH:i'),
            'account_id' => $request->account_id,
            'other_sale_categories' => CacheOtherSaleCategory::get(),
            'accounts' => CacheAccount::get(),
        ];

        return Inertia::render('Operation/OtherSale/Create', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @param  OtherSale  $other_sale
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'check_no' => ['nullable', 'string', 'unique:transactions'],
            'description' => ['nullable', 'string', 'max:500'],
            'other_sale_category_id' => ['required', 'exists:other_sale_categories,id'],
            'account_id' => ['required', 'exists:accounts,id'],
        ]);

        DB::beginTransaction();

        $transaction = new Transaction;
        $transaction->date = now()->parse($request->date);
        $transaction->check_no = $request->check_no;
        $transaction->amount = $request->amount;
        $transaction->direction = 'out';
        $transaction->account_id = $request->account_id;
        $transaction->creator_id = $request->user()->id;
        $transaction->save();

        $other_sale = new OtherSale;
        $other_sale->date = now()->parse($request->date);
        $other_sale->amount = $request->amount;
        $other_sale->description = $request->description;
        $other_sale->account_id = $request->account_id;
        $other_sale->branch_id = UseBranch::id();
        $other_sale->transaction_id = $transaction->id;
        $other_sale->other_sale_category_id = $request->other_sale_category_id;
        $other_sale->save();

        DB::commit();
        CacheOtherSale::forget();
        CacheTransaction::forget();

        return redirect()->route('other_sale.index')->with('success', 'OtherSale created successfully.');
    }

    /**
     * Show the other_sale detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(OtherSale $other_sale)
    {
        $params = [
            'other_sale' => $other_sale,
        ];

        return Inertia::render('Operation/OtherSale/Show', $params);
    }

    /**
     * Show the other_sale edit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(OtherSale $other_sale)
    {
        $params = [
            'other_sale' => $other_sale,
            'accounts' => CacheAccount::get(),
            'other_sale_categories' => CacheOtherSaleCategory::get(),
        ];

        return Inertia::render('Operation/OtherSale/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, OtherSale $other_sale)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'check_no' => ['nullable', 'string', Rule::unique('transactions')->ignore($other_sale->transaction)],
            'description' => ['nullable', 'string', 'max:500'],
            'other_sale_category_id' => ['required', 'exists:other_sale_categories,id'],
            'account_id' => ['required', 'exists:accounts,id'],
        ]);

        DB::beginTransaction();

        $transaction = $other_sale->transaction;
        $transaction->date = now()->parse($request->date);
        $transaction->check_no = $request->check_no;
        $transaction->amount = $request->amount;
        $transaction->account_id = $request->account_id;
        $transaction->update();

        $other_sale->date = now()->parse($request->date);
        $other_sale->amount = $request->amount;
        $other_sale->description = $request->description;
        $other_sale->account_id = $request->account_id;
        $other_sale->branch_id = $request->branch_id;
        $other_sale->other_sale_category_id = $request->other_sale_category_id;
        $other_sale->update();

        DB::commit();
        CacheOtherSale::forget();
        CacheTransaction::forget();

        return back()->with('success', 'OtherSale updated successfully.');
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(OtherSale $other_sale)
    {
        DB::beginTransaction();
        $other_sale->delete();

        DB::commit();
        CacheOtherSale::forget();

        return redirect()->route('other_sale.index')->with('success', __('OtherSale removed successfully!'));
    }
}
