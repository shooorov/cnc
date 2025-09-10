<?php

namespace App\Http\Controllers\Manage;

use App\Http\Cache\CacheBranch;
use App\Http\Cache\CacheBranchAccess;
use App\Http\Cache\CacheProductCategory;
use App\Http\Cache\CacheRole;
use App\Http\Cache\CacheTokenAmount;
use App\Http\Cache\CacheUser;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchAccess;
use App\Models\Status;
use App\Models\TokenAmount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\UseBranch;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use App\Helpers;
use App\Http\Controllers\Manage\DateTime;
use DateTime as GlobalDateTime;

class BranchController extends Controller
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
    private static $seconds = 300;

    /**
     * Show the Branch list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $page_branches = Branch::all();

        $params = [
            'page_branches' => $page_branches,
        ];

        return Inertia::render('Manage/Branch/Index', $params);
    }

    /**
     * Show the Branch create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $params = [];

        return Inertia::render('Manage/Branch/Create', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @param  Branch  $branch
     * @return Response
     */
    public function store(Request $request)
    {

        // dd($request->selectedMonth);
        // die();
        $currentYear = date("Y");
        $currentMonth = date("F");

        $request->validate([
            'short_code' => ['required', 'string', 'max:10', 'unique:branches,short_code'],
            'name' => ['required', 'string', 'max:191', 'unique:branches,name'],
            'address' => ['nullable', 'string', 'max:500'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sale_target' => ['nullable', 'numeric', 'min:0'],
            'opening_hours' => ['nullable', 'string', 'max:191'],
            'pos_end_line' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        $branch = new Branch;
        $branch->name = $request->name;
        $branch->short_code = $request->short_code;
        $branch->phone = $request->contact_number;
        $branch->address = $request->address;
        $branch->vat_rate = $request->vat_rate ?? 0;
        $branch->sale_target = $request->sale_target ?? 0;
        $branch->opening_hours = $request->opening_hours;
        $branch->pos_end_line = $request->pos_end_line;
        $branch->save();
        // Get the ID of the newly created branch
        $branchId = $branch->id;
        DB::table('sale_targets')->insert([
            'branch_id' => $branchId,
            'target_month' => $currentMonth,
            'target_year' => $currentYear,
            'target_amount' => $request->sale_target ?? 0,
        ]);

        DB::commit();
        CacheBranch::forget();

        return redirect()->route('branch.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Show the Branch detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Branch $branch)
    {
        $params = [
            'branch' => $branch,
        ];

        return Inertia::render('Manage/Branch/Show', $params);
    }

    /**
     * Show the Branch edit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(Branch $branch)
    {
        // dd($branch);
        // die();
        $params = [
            'selected_branch' => $branch,
            'statuses' => $branch->statuses,
            'module' => [
                'production' => config('module.production'),
                'delivery' => config('module.delivery'),
            ],
        ];

        return Inertia::render('Manage/Branch/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Branch $branch)
    {
        $date = Helpers::dayStartedAt();
        $today = (clone $date);
        $yesterday = (clone $date)->subDay();
        $current_month = (clone $date);
        $last_month = (clone $current_month)->subMonth();
        $end_date = Helpers::dayEndedAt($last_month->endOfMonth());
        $start_date = Helpers::dayStartedAt($last_month->startOfMonth());
        $currentYear = date("Y");
        $currentMonth = date("F");
        $branchId = $branch->id;


        $last_month_sale = Order::where('branch_id', $branchId)
            ->where('status', 'complete')
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date)
            ->sum('total');

        // dd($last_month_sale, $last_month, $start_date, $end_date);
        // die();
        $achivedordeficit = $request->sale_target - $last_month_sale;
        $request->validate([
            'short_code' => ['required', 'string', 'max:10', Rule::unique('branches')->ignore($branch)],
            'name' => ['required', 'string', 'max:191', Rule::unique('branches')->ignore($branch)],
            'address' => ['nullable', 'string', 'max:500'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'opening_hours' => ['nullable', 'string', 'max:191'],
            'service_cost' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'barista_target' => ['nullable', 'string', 'max:191'],
            'chef_target' => ['nullable', 'string', 'max:191'],
            'sale_target' => ['nullable', 'numeric', 'min:0'],

            'delivery_cost' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'pos_end_line' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();

        $branch->name = $request->name;
        $branch->short_code = $request->short_code;
        $branch->phone = $request->contact_number;
        $branch->address = $request->address;
        $branch->vat_rate = $request->vat_rate ?? 0;
        $branch->opening_hours = $request->opening_hours;
        $branch->service_cost = $request->service_cost ?? 0;
        $branch->barista_target = $request->barista_target;
        $branch->chef_target = $request->chef_target;
        $branch->sale_target = $request->sale_target ?? 0;

        $branch->delivery_cost = $request->delivery_cost ?? 0;
        $branch->pos_end_line = $request->pos_end_line;
        $branch->update();
        // Create a DateTime object for the current month and year
        $currentDateTime = new GlobalDateTime($currentYear . '-' . $currentMonth);

        // Subtract one month to get the previous month
        $previousDateTime = clone $currentDateTime;
        $previousDateTime->modify('-1 month');

        // Extract the previous month and year
        $previousMonth = $previousDateTime->format("F");
        $previousYear = $previousDateTime->format("Y");

        // Now, you can use $previousMonth and $previousYear in your queries

        // Find the record for the previous month
        $previousMonthTarget = DB::table('sale_targets')
            ->where('branch_id', $branchId)
            ->where('target_month', $previousMonth)
            ->where('target_year', $previousYear)
            ->first();

        // Update the previous month record if it exists
        if ($previousMonthTarget) {
            DB::table('sale_targets')
                ->where('id', $previousMonthTarget->id)
                ->update([
                    'achived_amount' => $last_month_sale,
                    'deficit_amount' => $achivedordeficit
                ]);
        }


        // Check if the record exists
        $existingTarget = DB::table('sale_targets')
            ->where('branch_id', $branchId)
            ->where('target_month', $currentMonth)
            ->where('target_year', $currentYear)
            ->first();

        if ($existingTarget) {
            // Update the existing record
            DB::table('sale_targets')
                ->where('id', $existingTarget->id)
                ->update(['target_amount' => $request->sale_target ?? 0]);
        } else {
            // Insert a new record
            DB::table('sale_targets')->insert([
                'branch_id' => $branchId,
                'target_month' => $currentMonth,
                'target_year' => $currentYear,
                'target_amount' => $request->sale_target ?? 0,
            ]);
            // DB::table('sale_targets')
            // ->where('branch_id', $branchId)
            // ->update([
            //     'achived_amount' => $last_month_sale,
            //     'deficit_amount' => $achivedordeficit
            // ]);

        }



        DB::commit();
        CacheBranch::forget();

        return back()->with('success', 'Branch updated successfully.');
    }

    /**
     * Change status the specified resource in storage.
     *
     * @return Response
     */
    public function updateStatus(Request $request, Branch $branch)
    {
        if (! array_key_exists($request->status, $branch->statuses)) {
            return back()->with('fail', 'Status changing request failed! Invalid status!');
        }

        if ($branch->status == $request->status) {
            return back()->with('fail', 'Status already changed!');
        }

        DB::beginTransaction();
        $branch->changeStatuses()->save(new Status([
            'previous_status' => $branch->status ?? '',
            'changed_status' => $request->status,
            'user_id' => $request->user()->id,
        ]));

        $branch->status = $request->status;
        $branch->save();

        DB::commit();
        CacheBranch::forget();

        return back()->with('success', 'Status changed to "' . $branch->statuses[$request->status] . '" successfully');
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Branch $branch)
    {
        DB::beginTransaction();
        if ($branch->orders->count() > 0) {
            return back()->with('fail', __('Branch removing failed!'));
        }
        $branch->delete();
        DB::commit();
        CacheBranch::forget();

        return redirect()->route('branch.index')->with('success', __('Branch removed successfully!'));
    }

    public function access()
    {
        $users = CacheUser::get();
        $page_roles = CacheRole::get();
        $access = CacheBranchAccess::get()->groupBy('user_id')->map->pluck('is_checked', 'branch_id')->toArray();

        $params = [
            'users' => $users,
            'access' => $access,
            'page_roles' => $page_roles,
        ];

        return Inertia::render('Manage/Branch/Access', $params);
    }

    public function accessUpdate(Request $request)
    {
        $request->validate([
            'access' => ['required', 'array'],
            'user_id' => ['required', 'exists:users,id'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        BranchAccess::updateOrCreate([
            'user_id' => $request->user_id,
            'branch_id' => $request->branch_id,
        ], [
            'is_checked' => $request->access[$request->user_id][$request->branch_id],
        ]);

        CacheBranchAccess::forget();

        return back()->with('success', __('Branch Access modified successfully!'));
    }

    public function token()
    {
        $categories = CacheProductCategory::get();
        $page_branches = CacheBranch::get();

        $params = [
            'categories' => $categories,
            'page_branches' => $page_branches,
        ];

        return Inertia::render('Manage/Branch/Token', $params);
    }

    public function tokenUpdate(Request $request, Branch $branch)
    {
        $request->validate([
            'categories' => ['required', 'array'],
        ]);

        DB::beginTransaction();

        foreach ($request->categories as $user) {

            $token_amount = $user['token_amount'];

            foreach ($token_amount as $branch => $amount) {
                TokenAmount::updateOrCreate([
                    'product_category_id' => $user['id'],
                    'branch_id' => $branch,
                ], [
                    'amount' => $amount ?? 0,
                ]);
            }
        }

        DB::commit();
        CacheUser::forget();
        CacheTokenAmount::forget();

        return back()->with('success', __('Token amount modified successfully!'));
    }
}
