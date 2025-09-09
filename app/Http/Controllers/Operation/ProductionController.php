<?php

namespace App\Http\Controllers\Operation;

use App\Data;
use App\Events\ProductionCardModified;
use App\Helpers;
use App\Http\Cache\CacheProduction;
use App\Http\Cache\CacheProductionItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\Production as ResourcesProduction;
use App\Http\Resources\ProductionCollection;
use App\Models\KitchenLog;
use App\Models\Production;
use App\Models\Status;
use App\UseBranch;
use App\UseKitchen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductionController extends Controller
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

    public function index(Request $request)
    {
        $end_date = Helpers::dayEndedAt();
        $start_date = Helpers::dayStartedAt();

        $order_id = $request->order_id;
        $waiter_id = Auth::user()->is_waiter ? Auth::user()->id : null;
        $type = $request->user()->is_cook ? $request->user()->type : null;

        // take only incomplete and not selected productions
        $productions = Production::with('items')
            ->where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date)
            ->whereNull('delivered_at')
            // ->whereNull('completed_at')
            ->where('order_id', '!=', $order_id)
            ->whereHas('order', function ($query) use ($waiter_id) {
                $query->whereIn('status', ['pending', 'accept'])
                    ->when($waiter_id, function ($query, $waiter_id) {
                        $query->where('waiter_id', $waiter_id);
                    });
            })
            ->when($type, function ($query, $type) {
                $query->whereType($type)->whereNull('completed_at');
            })
            ->get();

        $today_completed_productions = Production::where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date)
            ->where('status', 'complete')
            ->when($type, function ($query, $type) {
                $query->whereType($type);
            })
            ->get();

        $selected_productions = Production::with('items')
            ->where('order_id', $order_id)
            ->when($type, function ($query, $type) {
                $query->whereType($type);
            })
            ->get();

        $today_total_duration = $today_completed_productions->sum('duration');
        $today_total_quantity = $today_completed_productions->sum('quantity');

        $total_duration = Production::sum('duration');
        $total_quantity = Production::sum('quantity');

        $average_time = $total_quantity ? (($total_duration % 60) / $total_quantity) : 0;
        $today_average_time = $today_total_quantity ? (($today_total_duration % 60) / $today_total_quantity) : 0;

        switch ($type) {
            case 'chef':
                $target_time = UseBranch::take('chef_target');
            case 'barista':
                $target_time = UseBranch::take('barista_target');
            default:
                $target_time = 0;
        }

        $pending_orders = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->pendingOrders();

		$production_statuses = $productions->groupBy('status')->map->count()->toArray();

        $params = [
            'pending_orders' => $pending_orders,
            'production_statuses' => $production_statuses,
            'target_time' => round($target_time, 2).' Mins',
            'average_time' => round($average_time, 2).' Mins',
            'today_average_time' => round($today_average_time, 2).' Mins',
            'productions' => ResourcesProduction::collection($productions)->toArray($request),
            'order_productions' => ($order_id) ? ResourcesProduction::collection($selected_productions)->toArray($request) : [],
        ];

		return Inertia::render('Operation/Production', $params);
    }

    public function status(Request $request)
    {
        $kitchen_log = KitchenLog::find(UseKitchen::id());

        if (! $kitchen_log) {
            return back()->with('fail', 'Kitchen not running yet!');
        }

        $production = Production::findOrFail($request->production_id);

        $prev_status = $production?->status;
        $next_status = null;

        $valid_statuses = array_keys((new Production)->statuses);
        $current_index = array_search($prev_status, $valid_statuses);

        if (isset($valid_statuses[$current_index + 1])) {
            $next_status = $valid_statuses[$current_index + 1];
        }

        if ($request->status != 'next' || ! $next_status) {
            return back()->with('fail', 'Production Status changing failed! invalid status');
        }

        DB::beginTransaction();

        try {
            $production->changeStatuses()->save(new Status([
                'previous_status' => $prev_status,
                'changed_status' => $next_status,
                'user_id' => $request->user()->id,
            ]));

            $production->update([
                'status' => $next_status,
                'accepted_at' => $next_status == 'accept' ? now() : $production->accepted_at,
                'completed_at' => $next_status == 'complete' ? now() : $production->completed_at,
                'duration' => $next_status == 'complete' ? now()->diffInSeconds($production->accepted_at) : $production->duration,
                'kitchen_log_id' => $production->kitchen_log_id ?? $kitchen_log->id,
            ]);

            DB::commit();

            CacheProduction::forget();
            CacheProductionItem::forget();
            // ProductionCardModified::dispatch($production);

            return back()->with('success', 'Production Status changed to "'.ucfirst($next_status).'"');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('fail', 'Production Status changing failed!');
        }
    }

    public function view(Request $request)
    {
        if (! KitchenLog::find(UseKitchen::id())) {
            return back()->with('fail', 'Kitchen not running yet!');
        }

        $production = Production::findOrFail($request->production_id);

        if ($production->type != $request->user()->type) {
            return back()->with('fail', 'This card is for '.ucfirst($production->type).'!');
        }

        if (empty($production->viewed_at)) {
            $production->update([
                'viewed_at' => now(),
            ]);
            CacheProduction::forget();
            CacheProductionItem::forget();
            // ProductionCardModified::dispatch($production);
        }

        return back();
    }

    public function delivered(Request $request)
    {
        $production = Production::findOrFail($request->production_id);

        if (empty($production->delivered_at)) {
            DB::beginTransaction();

            $production->update([
                'delivered_at' => now(),
            ]);

            DB::commit();
            CacheProduction::forget();
            CacheProductionItem::forget();
            // ProductionCardModified::dispatch($production);
        }

        return back()->with('success', 'Food served to customer!');
    }
}
