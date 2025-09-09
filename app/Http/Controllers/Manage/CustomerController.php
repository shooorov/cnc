<?php

namespace App\Http\Controllers\Manage;

use App\Http\Cache\CacheCustomer;
use App\Http\Cache\CacheUser;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the Customer list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $customers = CacheCustomer::get();

        $params = [
            'customers' => $customers,
        ];

        return Inertia::render('Manage/Customer/Index', $params);
    }

    /**
     * Show the Customer create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $users = CacheUser::get()->filter(function ($user) {
            return $user->is_admin;
        })->values();

        $params = [
            'users' => $users,
        ];

        return Inertia::render('Manage/Customer/Create', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @param  Customer  $customer
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'mobile' => ['required', 'unique:customers'],
            'user_id' => ['nullable', 'unique:customers'],
        ]);

        DB::beginTransaction();
        $customer = new Customer;
        $customer->name = $request->name;
        $customer->mobile = $request->mobile;
        $customer->user_id = $request->user_id;
        $customer->save();

        DB::commit();
        CacheCustomer::forget();

        return redirect()->route('customer.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Show the Customer detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Customer $customer)
    {
        $params = [
            'customer' => $customer,
        ];

        return Inertia::render('Manage/Customer/Show', $params);
    }

    /**
     * Show the Customer edit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(Customer $customer)
    {
        $users = CacheUser::get()->filter(function ($user) {
            return $user->is_admin;
        })->values();

        $params = [
            'users' => $users,
            'customer' => $customer,
        ];

        return Inertia::render('Manage/Customer/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'mobile' => ['required', Rule::unique('customers')->ignore($customer)],
            'user_id' => ['nullable', Rule::unique('customers')->ignore($customer)],
        ]);

        DB::beginTransaction();
        $customer->name = $request->name;
        $customer->mobile = $request->mobile;
        $customer->user_id = $request->user_id;
        $customer->update();

        DB::commit();
        CacheCustomer::forget();

        return back()->with('success', 'Customer updated successfully.');
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Customer $customer)
    {
        DB::beginTransaction();
        $customer->delete();

        DB::commit();
        CacheCustomer::forget();

        return redirect()->route('customer.index')->with('success', __('Customer removed successfully!'));
    }
}
