<?php

namespace App\Http\Controllers;

use App\Data;
use App\Helpers;
use App\Http\Cache\CacheCustomerOrder;
use App\Http\Cache\CacheData;
use App\Http\Cache\CacheOrder;
use App\Http\Cache\CacheOrderProduct;
use App\Http\Cache\CacheOrderProductQuantity;
use App\Http\Cache\CacheProduction;
use App\Http\Cache\CacheProductionItem;
use App\Models\Branch;
use App\Models\OrderApproval;
use App\Models\Status;
use App\Providers\RouteServiceProvider;
use App\RolePermission;
use App\UseBranch;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
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

    public function index(Request $request)
    {
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');

		$nextRedirectTo = "";

		if ($request->user()->is_waiter) {
			$nextRedirectTo = route('pos.create');
		} elseif ($request->user()->is_barista || $request->user()->is_chef) {
			$nextRedirectTo = route('production.index');
		} elseif ($request->user()->is_rider) {
			$nextRedirectTo = route('delivery.index');
		}

		if(UseBranch::get() && $nextRedirectTo) {
			return redirect()->intended($nextRedirectTo);
		}

		$request->session()->put('url.intended', $nextRedirectTo);

		return redirect()->route('dashboard')->with('fail', session()->get('fail'));
    }

    public function use(Request $request)
    {
		$branchId = $request->branch_id;
		$branch = null;
        // var_dump($branchId);
        // die();
		if($branchId) {
			$branch = UseBranch::get($branchId);
	
			if (! $branch) {
				return back()->with('fail', __('Invalid branch selection! You are not allowed to use this branch'));
			}
		}
		
		UseBranch::set($branch);

		return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Show the application data.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard(Request $request)
    {
        $branchId = $request->branch_id;
		$branch = null;
        $branchId = UseBranch::id();
        $branch_data = Branch::all()->where('id',$branchId);
        $branch = $branch_data->first();
        if ($branch){
            $sale_target = $branch->sale_target;

        }else{
            $sale_target = "";

        }
        
        // var_dump($sale_target);


        
        $isSuperAdmin = $request->user()->is_admin;

        
        $date = Helpers::dayStartedAt();
        $today = (clone $date);
        $yesterday = (clone $date)->subDay();
        $current_month = (clone $date);
        //$last_month = (clone $current_month)->subMonth();
        $last_month = (clone $date)->subMonth();
        $sale_branches = [];
        $branch_sales = [];
        

        $branch_id = UseBranch::id();
    
        // $current_month_start = now()->startOfMonth();
        // $current_month_end = now()->endOfMonth();
        $current_month_end = Helpers::dayEndedAt($current_month->endOfMonth());
        $current_month_start = Helpers::dayStartedAt($current_month->startOfMonth());
        // dd($current_month_start,$current_month_end);
        // die();

        $current_month_sale = Cache::remember('sale_current_month_'.$branchId, self::$seconds, function () use ($branchId, $current_month_start, $current_month_end) {
            return Order::where('branch_id', $branchId)
                        ->where('status', 'complete')
                        ->where('date', '>=', $current_month_start)
                        ->where('date', '<=', $current_month_end)
                        ->sum('total');
        });
        // dd($sale_target    , $current_month_sale);
        // die();
        // dd($current_month_start);
        // die();
        if (RolePermission::isEnabled('data.current_per_head_sale')) {

            $branch_sales = CacheData::perHeadSale($today);

            $sale_branches = UseBranch::available()->map(function ($item) use ($branch_sales) {

                $branch_sale = $branch_sales->first(fn ($bs) => $bs->branch_id == $item->id);

                $item->total_guest = $branch_sale?->total_guest ?? 0;
                $item->total_amount = $branch_sale?->total_amount ?? 0;

                $head_cost = $item->total_guest > 0 ? $item->total_amount / $item->total_guest : 0;

                $item->per_head_cost = Helpers::toAmount($head_cost);

                return $item;
            });
        }

        $cards = [];

        // dd(RolePermission::isEnabled('data.last_month_this_day_sale'));


        if (RolePermission::isEnabled('data.current_sale')) {
            $extraInfo = [];
        
            if (RolePermission::isEnabled('data.last_month_this_day_sale')) {
                $extraInfo['extra_info'] = [
                    'name' => 'Last Month On This Day Sale Was',
                    'amount' => Helpers::toAmount(CacheData::LastMonthThisDaySale($isSuperAdmin, $today)),
                ];
            }
        
            if (RolePermission::isEnabled('data.last_year_this_day_sale')) {
            $extraInfo['extra_info2'] = [
                'name' => 'Last Year On This Day Sale was',
                'amount' => Helpers::toAmount(CacheData::LastYearToday($isSuperAdmin, $today)),
            ];
            }

            if (RolePermission::isEnabled('data.yeasterday_till_this_time_sale')) {
                $extraInfo['extra_info3'] = [
                    'name' => 'Yesterday till this time sale was ',
                    'amount' => Helpers::toAmount(CacheData::yesterdaySaletillthistime($isSuperAdmin, $today)),
                ];
                }
           
        
            $cards[] = [
                'name' => 'Current Sale',
                'icon' => 'CashIcon',
                'amount' => Helpers::toAmount(CacheData::todaySale($isSuperAdmin, $today)),
                'count_record' => round(CacheData::todaySaleCount($isSuperAdmin, $today)) . ' Orders',
                'href' => route('order.index', [
                    'end_date' => $today->format('Y-m-d'),
                    'start_date' => $today->format('Y-m-d'),
                ]),
            ] + $extraInfo;
        }
        
        if (RolePermission::isEnabled('data.current_month_sale')) {

            $extraInfo = [];
        
            if (RolePermission::isEnabled('data.last_month_till_this_day_sale')) {
                $extraInfo['extra_info'] = [
                    'name' => 'Last Month Till Today Sale was ',
                    'amount' => Helpers::toAmount(CacheData::LastMonthTillToday($isSuperAdmin,$today)),
                ];
            }
        
            if (RolePermission::isEnabled('data.last_year_till_this_day_sale')) {
            $extraInfo['extra_info2'] = [
                'name' => 'Last Year This Month Sale was ',
                'amount' => Helpers::toAmount(CacheData::LastYearTillToday($isSuperAdmin,$today)),
            ];
            }

            $cards[] = [
                      'name' => 'Current Month Sale',
                    
                    'icon' => 'CashIcon',
                    'amount' => Helpers::toAmount(CacheData::currentMonthSale($current_month)),
                    'count_record' => round(CacheData::currentMonthSaleCount($current_month)).' Orders',
                    'href' => route('order.index', [
                        'end_date' => (clone $current_month)->endOfMonth()->format('Y-m-d'),
                        'start_date' => (clone $current_month)->startOfMonth()->format('Y-m-d'),
                    ]),
            ] + $extraInfo;
            // $cards = array_merge($cards, [
            //     [
            //         'name' => 'Current Month Sale',
                    
            //         'icon' => 'CashIcon',
            //         'amount' => Helpers::toAmount(CacheData::currentMonthSale($current_month)),
            //         'count_record' => round(CacheData::currentMonthSaleCount($current_month)).' Orders',
            //         'href' => route('order.index', [
            //             'end_date' => (clone $current_month)->endOfMonth()->format('Y-m-d'),
            //             'start_date' => (clone $current_month)->startOfMonth()->format('Y-m-d'),
            //         ]),
            //         'extra_info' => [
            //             'name' => 'Last Month Till Today Sale was ',
            //             'amount' => Helpers::toAmount(CacheData::LastMonthTillToday($isSuperAdmin,$today)), // You can dynamically calculate this value as needed
            //         ],
            //         'extra_info2' => [
            //             'name' => 'Last Year This Month Sale was ',
            //             'amount' => Helpers::toAmount(CacheData::LastYearTillToday($isSuperAdmin,$today)), // You can dynamically calculate this value as needed
            //         ],
            //     ],
            // ]);
        }
        
        // if (RolePermission::isEnabled('data.current_sale')) {
        //     $cards = array_merge($cards, [
        //         [
        //             'name' => 'Current Sale',
        //             'icon' => 'CashIcon',
        //             'amount' => Helpers::toAmount(CacheData::lastyear($isSuperAdmin,$today)),
        //             'count_record' => round(CacheData::todaySaleCount($isSuperAdmin,$today)).' Orders',
        //             'href' => route('order.index', [
        //                 'end_date' => $today->format('Y-m-d'),
        //                 'start_date' => $today->format('Y-m-d'),
        //             ]),
        //         ],
        //     ]);
        // }

        if (RolePermission::isEnabled('data.yesterday_sale')) {
            $cards = array_merge($cards, [
                [
                    'name' => 'Yesterday Sale',
                    'icon' => 'CashIcon',
                    'amount' => Helpers::toAmount(CacheData::yesterdaySale($yesterday)),
                    'count_record' => round(CacheData::yesterdaySaleCount($yesterday)).' Orders',
                    'href' => route('order.index', [
                        'end_date' => $yesterday->format('Y-m-d'),
                        'start_date' => $yesterday->format('Y-m-d'),
                    ]),
                    
                ],
            ]);
        }

        

        if (RolePermission::isEnabled('data.last_month_sale')) {
            $cards = array_merge($cards, [
                [
                    'name' => 'Last Month Sale',
                    'icon' => 'CashIcon',
                    'amount' => Helpers::toAmount(CacheData::lastMonthSale($last_month)),
                    'count_record' => round(CacheData::lastMonthSaleCount($last_month)).' Orders',
                    'href' => route('order.index', [
                        // 'end_date' => (clone $last_month)->endOfMonth()->format('Y-m-d'),
                        // 'start_date' => (clone $last_month)->startOfMonth()->format('Y-m-d'),
                        'end_date' => Helpers::dayEndedAt($last_month->endOfMonth()),
                        'start_date' => Helpers::dayStartedAt($last_month->startOfMonth()),
                        

                        // dd((clone $last_month)->startOfMonth()->format('Y-m-d')),
                        // die(),
                    ]),
                ],
            ]);
        }

        if (RolePermission::isEnabled('data.current_month_expense')) {
            $cards = array_merge($cards, [
                [
                    'name' => 'Current Month Expense',
                    'icon' => 'CashIcon',
                    'amount' => Helpers::toAmount(CacheData::currentMonthExpense($current_month)),
                    'href' => route('expense.index', [
                        'end_date' => (clone $current_month)->endOfMonth()->format('Y-m-d'),
                        'start_date' => (clone $current_month)->startOfMonth()->format('Y-m-d'),
                    ]),
                ],

            ]);
        }

        if (RolePermission::isEnabled('data.last_month_expense')) {
            $cards = array_merge($cards, [
                [
                    'name' => 'Last Month Expense',
                    'icon' => 'CashIcon',
                    'amount' => Helpers::toAmount(CacheData::lastMonthExpense($last_month)),
                    'href' => route('expense.index', [
                        'end_date' => (clone $last_month)->endOfMonth()->format('Y-m-d'),
                        'start_date' => (clone $last_month)->startOfMonth()->format('Y-m-d'),
                    ]),
                ],

            ]);
        }

        if (RolePermission::isEnabled('data.last_month_profit')) {
            $cards = array_merge($cards, [
                [
                    'name' => 'Last Month Profit',
                    'icon' => 'CashIcon',
                    'amount' => Helpers::toAmount(
                        (CacheData::lastMonthSale($last_month) - CacheData::lastMonthExpense($last_month))
                    ),
                    'href' => '#',
                ],

            ]);
        }

        if (RolePermission::isEnabled('data.avg_bucket_size_current_month')) {
            $cards = array_merge($cards, [
                [
                    'name' => 'Average Bucket Size (Current Month)',
                    'icon' => 'CashIcon',
                    'amount' => Helpers::toAmount(
                        CacheData::currentMonthSale($current_month)
                            / (CacheData::currentMonthSaleCount($current_month) > 0 ? CacheData::currentMonthSaleCount($current_month) : 1)
                    ),
                    'href' => '#',
                ],

            ]);
        }

        if (RolePermission::isEnabled('data.avg_bucket_size_till_today')) {
            $cards = array_merge($cards, [
                [
                    'name' => 'Average Bucket Size (Till Today)',
                    'icon' => 'CashIcon',
                    'amount' => Helpers::toAmount(
                        CacheData::totalSale()
                            / (CacheData::totalSaleCount() > 0 ? CacheData::totalSaleCount() : 1)
                    ),
                    'href' => '#',
                ],

            ]);
        }

        $last_24_hour = (clone $date)->subHour(24)->startOfHour();
        $data_hourly = CacheData::hourly($last_24_hour);

        $last_30_day = (clone $date)->subDay(30)->startOfDay();
        $data_daily = CacheData::daily($last_30_day);

        $last_6_month = (clone $date)->subMonth(5)->startOfMonth();
        $data_monthly = CacheData::monthly($last_6_month);


        // $branchId = 1;
// 1. Last Month on This Day
$lastMonthSameDay = $today->copy()->subMonthNoOverflow();
$lastMonthSameDaySales = Order::where('branch_id', $branchId)
                            //    ->where('status', 'complete')
                               ->whereDate('date', $lastMonthSameDay)
                               ->sum('total');


// 2. Last Month Till Today
$lastMonthStart = $today->copy()->subMonthNoOverflow()->startOfMonth();
$lastMonthToday = $today->copy()->subMonthNoOverflow();
// dd($lastMonthToday);
// die();
$lastMonthTillTodaySales = Order::where('branch_id', $branchId)
                                //  ->where('status', 'complete')
                                 ->whereBetween('date', [$lastMonthStart, $lastMonthToday])
                                 ->sum('total');

                                //  dd($lastMonthTillToday);
                                //  dd($lastMonthTillToday);
                                //  die();
// 3. Last Year on This Day
$lastYearSameDay = $today->copy()->subYear();
$lastYearSameDaySales = Order::where('branch_id', $branchId)
                            //   ->where('status', 'complete')
                              ->whereDate('date', $lastYearSameDay)
                              ->sum('total');

// 4. Last Year on This Month
$lastYearMonthStart = $today->copy()->subYear()->startOfMonth();
$lastYearMonthEnd = $today->copy()->subYear()->endOfMonth();
$lastYearMonthSales = Order::where('branch_id', $branchId)
                            // ->where('status', 'complete')
                            ->whereBetween('date', [$lastYearMonthStart, $lastYearMonthEnd])
                            ->sum('total');
        // var_dump($lastMonthSameDaySales);
        // var_dump($lastMonthTillTodaySales);
        // var_dump($lastYearSameDaySales);
        // var_dump($lastYearMonthSales);
        //die();

        $params = [
            'greeting' => Helpers::greeting(),
            'cards' => $cards,
            'sale_target' => $sale_target,
            'current_month_sale' => $current_month_sale,
            'lastMonthSameDaySales' =>$lastMonthSameDaySales,
            'lastMonthTillTodaySales' =>$lastMonthTillTodaySales,
            'lastYearSameDaySales' =>$lastYearSameDaySales,
            'lastYearMonthSales' =>$lastYearMonthSales,
            'sale_branches' => $sale_branches,
            'chart_data' => [
                'branches' => RolePermission::isEnabled('data.branches_pie_chart') ? [] : [],
                'hourly' => RolePermission::isEnabled('data.hourly_chart') ? $data_hourly : [],
                'daily' => RolePermission::isEnabled('data.daily_chart') ? $data_daily : [],
                'monthly' => RolePermission::isEnabled('data.monthly_chart') ? $data_monthly : [],
            ],
        ];
        // dd($params);
        // die();

        return Inertia::render('Dashboard', $params);
    }

    public function orderApproval(Request $request)
    {
        $o_approval = OrderApproval::where('order_id', $request->order_id)
            ->where('token', $request->token)
            ->first();

        if (! $o_approval) {
            abort(404);
        }

        $order = $o_approval->order;
        $order->products;

        $branch = $o_approval->order->branch;

        $discount_types = collect($order->discount_types);

        $params = [
            'status' => $o_approval->status,
            'branch' => $branch,
            'order' => $order,
            'token' => $o_approval->token,
            'discount_types' => $discount_types,
        ];

        return Inertia::render('Operation/Order/Approval', $params);
    }

    public function orderApprovalUpdate(Request $request)
    {
        $o_approval = OrderApproval::where('order_id', $request->order_id)
            ->where('token', $request->token)
            ->first();

        if (! $o_approval) {
            return back()->with('fail', 'Order is invalid!');
        }

        $order = $o_approval->order;

        DB::beginTransaction();

        if ($request->status == 'approved') {
            $order_status = 'complete';

            $order->changeStatuses()->save(new Status([
                'previous_status' => $order->status ?? '',
                'changed_status' => $order_status,
                'user_id' => $o_approval->user_id,
            ]));
            $order->status = $order_status;
            $order->save();
        }
        $o_approval->status = $request->status;
        $o_approval->save();

        DB::commit();
        CacheOrder::forget();
        CacheOrderProduct::forget();
        CacheProduction::forget();
        CacheProductionItem::forget();
        CacheOrderProductQuantity::forget();
        CacheCustomerOrder::forget();

        return back()->with('success', 'Order approval process status is '.$request->status);
    }
}
