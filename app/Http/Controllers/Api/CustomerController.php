<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function auth(Request $request)
    {
        $mobile = substr($request->mobile, 3);
        $customer = Customer::firstOrCreate([
            'mobile' => $mobile,
        ]);

        $params = [
            'customer' => $customer,
        ];

        return response()->json($params, 200);
    }

    public function update(Customer $customer, Request $request)
    {
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->update();

        $params = [
            'success' => true,
            'message' => 'Customer updated successfully!',
        ];

        return response()->json($params, 201);
    }
}
