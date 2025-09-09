<?php

namespace App\Http\Controllers\Manage;

use App\Http\Cache\CacheDesignation;
use App\Http\Cache\CacheEmployee;
use App\Http\Cache\CacheRole;
use App\Http\Cache\CacheSalary;
use App\Http\Cache\CacheUser;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EmployeeController extends Controller
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
        $employees = CacheEmployee::get();

        $params = [
            'employees' => $employees,
        ];

        return Inertia::render('Manage/Employee/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $designations = CacheDesignation::get();

        $params = [
            'designations' => $designations,
        ];

        return Inertia::render('Manage/Employee/Create', $params);
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
            'designation_id' => ['required', 'exists:designations,id'],
        ]);

        DB::beginTransaction();
        $employee = new Employee;
        $employee->name = $request->name;
        $employee->designation_id = $request->designation_id;
        $employee->save();

        DB::commit();
        CacheSalary::forget();
        CacheEmployee::forget();

        return redirect()->route('employee.edit', $employee->id)->with('success', __('Employee added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Employee $employee)
    {
        $params = [
            'employee' => $employee,
        ];
        return Inertia::render('Manage/Employee/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Employee $employee)
    {
        $params = [
            'employee' => $employee,
            'designations' => CacheDesignation::get(),
        ];

        return Inertia::render('Manage/Employee/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', Rule::unique('employees')->ignore($employee)],
            'designation_id' => ['required', 'exists:designations,id'],
            'gross' => ['nullable', 'numeric'],
            'basic' => ['nullable', 'numeric'],
            'rent' => ['nullable', 'numeric'],
            'medical' => ['nullable', 'numeric'],
            'transport' => ['nullable', 'numeric'],
            'food' => ['nullable', 'numeric'],
            'other' => ['nullable', 'numeric'],
            // 'bonus_rate'        => ['nullable', 'numeric'],
            'bonus' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();

        $user_emails = CacheUser::get()->pluck('email')->toArray();
        if ($request->email != $employee->email && in_array($employee->email, $user_emails)) {
            $user = User::where('email', $employee->email)->first();
            $user->email = $request->email;
            $user->update();
        }

        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->designation_id = $request->designation_id;
        $employee->save();

        $salary = Salary::updateOrCreate([
            'employee_id' => $employee->id,
        ], [
            'gross' => $request->gross,
            'basic' => $request->basic,
            'rent' => $request->rent,
            'medical' => $request->medical,
            'transport' => $request->transport,
            'food' => $request->food,
            'other' => $request->other,
            // 'bonus_rate'    => $request->bonus_rate,
            'bonus' => $request->bonus,
        ]);

        DB::commit();
        CacheSalary::forget();
        CacheEmployee::forget();

        return back()->with('success', __('Employee modified successfully!'));
    }

    /**
     * Making User the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function user(Employee $employee)
    {
        if (! $employee->email) {
            return back()->with('fail', __('Employee Email not found!'));
        }
        if (in_array($employee->email, User::pluck('email')->toArray())) {
            return back()->with('fail', __('Employee Email found!'));
        }

        DB::beginTransaction();

        $user = new User;
        $user->name = $employee->name;
        $user->email = $employee->email;
        $user->is_chef = strtolower($employee->role_name) == 'chef';
        $user->is_barista = strtolower($employee->role_name) == 'barista';
        $user->is_waiter = strtolower($employee->role_name) == 'waiter';
        $user->status = $user->default_status;
        $user->password = bcrypt($employee->email);
        $user->save();

        DB::commit();
        CacheUser::forget();
        CacheRole::forget();

        return back()->with('success', __('Employee Added as User successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Employee $employee)
    {
        DB::beginTransaction();
        $employee->delete();

        DB::commit();
        CacheSalary::forget();
        CacheEmployee::forget();

        return redirect()->route('employee.index')->with('success', __('Employee removed successfully!'));
    }
}
