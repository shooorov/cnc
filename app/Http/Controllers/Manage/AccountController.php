<?php

namespace App\Http\Controllers\Manage;

use App\Http\Cache\CacheAccount;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AccountController extends Controller
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
     * Show the accounts list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $accounts = CacheAccount::get();

        $params = [
            'accounts' => $accounts,
        ];

        return Inertia::render('Manage/Account/Index', $params);
    }

    /**
     * Show the account create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $params = [
            'types' => [
                ['id' => 'bank', 'name' => 'Bank'],
                ['id' => 'petty-cash', 'name' => 'Petty Cash'],
            ],
        ];

        return Inertia::render('Manage/Account/Create', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @param  Account  $account
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', 'max:191'],
            'name' => ['nullable', 'string', 'max:191'],
            'number' => ['required', 'string', 'max:191'],
            'branch' => ['required', 'string', 'max:191'],
            'address' => ['nullable', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $account = new Account;
        $account->type = $request->type;
        $account->name = $request->name;
        $account->number = $request->number;
        $account->branch = $request->branch;
        $account->address = $request->address;
        $account->save();

        DB::commit();
        CacheAccount::forget();

        return redirect()->route('account.index')->with('success', 'Account created successfully.');
    }

    /**
     * Show the account detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Account $account)
    {
        $params = [
            'account' => $account,
        ];

        return Inertia::render('Manage/Account/Show', $params);
    }

    /**
     * Show the account edit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(Account $account)
    {
        $params = [
            'account' => $account,
            'types' => [
                ['id' => 'bank', 'name' => 'Bank'],
                ['id' => 'petty-cash', 'name' => 'Petty Cash'],
            ],
        ];

        return Inertia::render('Manage/Account/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Account $account)
    {
        $opening_status = $request->opening_check ? 'required' : 'nullable';
        $request->validate([
            'type' => ['required', 'string', 'max:191'],
            'name' => ['nullable', 'string', 'max:191'],
            'number' => ['required', 'string', 'max:191'],
            'branch' => ['required', 'string', 'max:191'],
            'address' => ['nullable', 'string', 'max:191'],
            'opening_balance' => [$opening_status, 'max:16'],
            'opening_date' => [$opening_status, 'string'],
        ]);

        DB::beginTransaction();
        $account->type = $request->type;
        $account->name = $request->name;
        $account->number = $request->number;
        $account->branch = $request->branch;
        $account->address = $request->address;
        $account->opening_date = now()->parse($request->opening_date);
        $account->opening_balance = $request->opening_balance;
        $account->update();

        DB::commit();
        CacheAccount::forget();

        return redirect()->route('account.edit', $account->id)->with('success', 'Account created successfully.');
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Account $account)
    {
        DB::beginTransaction();
        $account->delete();

        DB::commit();
        CacheAccount::forget();

        return redirect()->route('account.index')->with('success', __('Account removed successfully!'));
    }
}
