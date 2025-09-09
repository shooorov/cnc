<?php

namespace App\Http\Controllers\Operation;

use App\Http\Cache\CacheKitchen;
use App\Http\Cache\CacheKitchenLog;
use App\Http\Cache\CacheUser;
use App\Http\Controllers\Controller;
use App\Models\KitchenLog;
use App\UseBranch;
use App\UseKitchen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KitchenController extends Controller
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
     * Show the kitchen_logs list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user_id = $request->user_id;
        $end_date = $request->end_date;
        $start_date = $request->start_date;

        $kitchen_logs = CacheKitchenLog::get()
            ->when(! Auth::user()->is_admin, function ($collection) {
                return $collection->where('user_id', Auth::user()->id);
            })->when($user_id, function ($collection) use ($user_id) {
                return $collection->where('user_id', $user_id);
            })->values();

        $params = [
            'users' => CacheUser::get(),
            'kitchen_logs' => $kitchen_logs,
            'today' => now()->format('Y-m-d'),
            'filter' => [
                'user_id' => $user_id,
                'end_date' => $end_date,
                'start_date' => $start_date,
            ],
        ];

        return Inertia::render('Operation/KitchenLog/Index', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @return Response
     */
    public function clock(Request $request)
    {
        if (! UseBranch::id()) {
            return back()->with('fail', 'Please specify branch first.');
        }

        $kitchen_log = KitchenLog::find(UseKitchen::id());

        if (! Auth::user()->is_cook) {
            return back()->with('fail', 'Chef / Barista can clock only');
        }

        if ($request?->status != 'start' && ! $kitchen_log) {
            return back()->with('fail', 'Kitchen clock is off.');
        }

        CacheKitchen::forget();
        UseKitchen::forget();

        DB::beginTransaction();

        if ($request->status == 'start') {
            $kitchen_log = new KitchenLog;
            $kitchen_log->started_at = now();
            $kitchen_log->type = Auth::user()->role_name;
            $kitchen_log->user_id = $request->user()->id;
            $kitchen_log->branch_id = UseBranch::id();
            $kitchen_log->save();

            UseKitchen::set($kitchen_log);

            DB::commit();

            return back()->with('success', 'Kitchen clock running successfully.');
        } elseif ($request->status == 'stop') {
            $kitchen_log->ended_at = now();
            $kitchen_log->update();

            DB::commit();

            return back()->with('success', 'Kitchen clock stopped successfully.');
        }

        return back();
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(KitchenLog $kitchen_log)
    {
        DB::beginTransaction();
        $kitchen_log->delete();

        DB::commit();
        CacheKitchenLog::forget();

        return redirect()->route('kitchen.index')->with('success', __('Kitchen log removed successfully!'));
    }
}
