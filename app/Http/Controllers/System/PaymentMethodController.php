<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CachePaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PaymentMethodController extends Controller
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $methods = CachePaymentMethod::get();

        $params = [
            'methods' => $methods,
        ];

        return Inertia::render('System/PaymentMethod/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $params = [];

        return Inertia::render('System/PaymentMethod/Create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $method = new PaymentMethod;
        $method->name = $request->name;
        $method->bg_color = $request->bg_color;
        $method->text_color = $request->text_color;
        $method->text_size = $request->text_size;
        $method->font_weight = $request->font_weight;
        $method->save();

        DB::commit();
        CachePaymentMethod::forget();

        return redirect()->route('method.index')->with('success', __('Payment Method added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(PaymentMethod $method)
    {
        $params = [
            'method' => $method,
        ];

        return Inertia::render('System/PaymentMethod/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(PaymentMethod $method)
    {
        $params = [
            'method' => $method,
        ];

        return Inertia::render('System/PaymentMethod/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, PaymentMethod $method)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();

        if ($method->id == 1 && $method->name != $request->name) {
            return back()->with('fail', __('Payment Method "Cash" name can\'t be updated'));
        }

        $method->name = $request->name;
        $method->bg_color = $request->bg_color;
        $method->text_color = $request->text_color;
        $method->text_size = $request->text_size;
        $method->font_weight = $request->font_weight;
        $method->update();

        DB::commit();
        CachePaymentMethod::forget();

        return back()->with('success', __('Payment Method modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(PaymentMethod $method)
    {
        if ($method->id == 1) {
            return back()->with('fail', __('Payment Method removing restricted'));
        }

        DB::beginTransaction();
        $method->delete();

        DB::commit();
        CachePaymentMethod::forget();

        return redirect()->route('method.index')->with('success', __('Payment Method removed successfully!'));
    }
}
