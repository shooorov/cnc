<?php

namespace App\Http\Controllers\System;

use App\Http\Cache\CacheExpenseCategory;
use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ExpenseCategoryController extends Controller
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
        $expense_categories = CacheExpenseCategory::get();

        $params = [
            'expense_categories' => $expense_categories,
        ];

        return Inertia::render('System/ExpenseCategory/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $expense_categories = CacheExpenseCategory::get()->filter(function ($category) {
            return is_null($category->expense_category_id);
        });

        $params = [
            'expense_categories' => $expense_categories,
        ];

        return Inertia::render('System/ExpenseCategory/Create', $params);
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
            'type' => ['nullable', 'string', 'max:191'],
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
        ]);

        DB::beginTransaction();
        $expense_category = new ExpenseCategory;
        $expense_category->name = $request->name;
        $expense_category->type = $request->type;
        $expense_category->expense_category_id = $request->expense_category_id;
        $expense_category->save();

        DB::commit();
        CacheExpenseCategory::forget();

        return redirect()->route('expense_category.index')->with('success', __('ExpenseCategory added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(ExpenseCategory $expense_category)
    {
        $params = [
            'expense_category' => $expense_category,
        ];

        return Inertia::render('System/ExpenseCategory/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(ExpenseCategory $expense_category)
    {
        $expense_categories = CacheExpenseCategory::get()->filter(function ($category) {
            return is_null($category->expense_category_id);
        });

        $params = [
            'expense_category' => $expense_category,
            'expense_categories' => $expense_categories,
        ];

        return Inertia::render('System/ExpenseCategory/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'type' => ['nullable', 'string', 'max:191'],
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
        ]);

        DB::beginTransaction();
        $expense_category->name = $request->name;
        $expense_category->type = $request->type;
        $expense_category->expense_category_id = $request->expense_category_id;
        $expense_category->update();

        DB::commit();
        CacheExpenseCategory::forget();

        return back()->with('success', __('ExpenseCategory modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(ExpenseCategory $expense_category)
    {
        DB::beginTransaction();
        $expense_category->delete();

        DB::commit();
        CacheExpenseCategory::forget();

        return redirect()->route('expense_category.index')->with('success', __('ExpenseCategory removed successfully!'));
    }
}
