<?php

namespace App\Http\Controllers\Operation;

use App\Http\Cache\CacheAccount;
use App\Http\Cache\CacheTransaction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Account as ResourcesAccount;
use App\Http\Resources\Transaction as ResourcesTransaction;
use App\Models\Transaction;
use App\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionController extends Controller
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

    /**
     * Show the Transaction list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $isDateSearch = RolePermission::isEnabled('record_search.transaction_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? now()->parse($request->end_date) : now();
            $start_date = $request->start_date ? now()->parse($request->start_date) : now()->startOfMonth();
        } else {
            $end_date = now();
            $start_date = now()->startOfMonth();
        }

        $account_id = $request->account_id;

        $transactions = CacheTransaction::get()
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date)
            ->when($account_id, function ($transactions, $account_id) {
                return $transactions->where('account_id', $account_id);
            });

        $params = [
            'accounts' => CacheAccount::get(),
            'transactions' => count($transactions) ? ResourcesTransaction::collection($transactions) : [],
            'filter' => [
                'account_id' => $account_id,
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/Transaction/Index', $params);
    }

    /**
     * Show the Transaction create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function transfer()
    {
        $accounts = CacheAccount::get();
        $params = [
            'accounts' => $accounts,
        ];

        return Inertia::render('Operation/Transaction/Transfer', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @param  Transaction  $transaction
     * @return Response
     */
    public function store(Request $request)
    {
        $add_type = $request->type == 'add' ? 'required' : 'nullable';
        $withdrawal_type = $request->type == 'withdrawal' ? 'required' : 'nullable';

        $request->validate([
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'check_no' => ['nullable', 'string', 'unique:transactions'],
            'to_account_id' => [$add_type, 'exists:accounts,id'],
            'from_account_id' => [$withdrawal_type, 'exists:accounts,id'],
        ]);

        $add_account = CacheAccount::find($request->to_account_id);
        $withdrawal_account = CacheAccount::find($request->from_account_id);

        DB::beginTransaction();
        if ($request->type != 'add') {
            $transaction = new Transaction;
            $transaction->date = $request->date;
            $transaction->amount = $request->amount;
            $transaction->check_no = $request->check_no;
            $transaction->account_id = $withdrawal_account->id;
            $transaction->direction = 'out';
            $transaction->creator_id = $request->user()->id;
            $transaction->save();
        }

        if ($request->type != 'withdrawal') {
            $transaction = new Transaction;
            $transaction->date = $request->date;
            $transaction->amount = $request->amount;
            $transaction->check_no = $request->check_no;
            $transaction->account_id = $add_account->id;
            $transaction->direction = 'in';
            $transaction->creator_id = $request->user()->id;
            $transaction->save();
        }
        DB::commit();
        CacheTransaction::forget();

        return back()->with('success', ucfirst($request->type).' of Transaction created successfully.');
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();
        $transaction->delete();

        DB::commit();
        CacheTransaction::forget();

        return redirect()->route('transaction.index')->with('success', __('Transaction removed successfully!'));
    }
}
