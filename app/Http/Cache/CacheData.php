<?php

namespace App\Http\Cache;

use App\Chart;
use App\Helpers;
use App\Models\Expense;
use App\Models\Order;
use App\UseBranch;
use App\RolePermission;
use Carbon\Carbon;
use DateTimeZone;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheData
{
    private static $seconds = 300;

    public static function perHeadSale($date)
    {
        $end_date = Helpers::dayEndedAt($date);
        $start_date = Helpers::dayStartedAt($date);

        return Cache::remember('per_head_sale_today', self::$seconds, function () use ($start_date, $end_date) {
            return DB::table('orders')
                ->select(
                    'branch_id',
                    DB::raw('SUM(total) as total_amount'),
                    DB::raw('SUM(guest_number) as total_guest')
                )->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->whereNull('orders.deleted_at')
                ->groupBy('branch_id')
                ->get();
        });
    }

    public static function todaySale_old($isSuperAdmin,$date)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($date);
        $start_date = Helpers::dayStartedAt($date);
        

        return Cache::remember('sale_today'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->sum('total');
        });
    }


    public static function todaySale($isSuperAdmin, $date)
{
    $branch_id = UseBranch::id();
    $end_date = Helpers::dayEndedAt($date);
    $start_date = Helpers::dayStartedAt($date);
    $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
    // dd($start_date,$end_date);
    // die();

    if ($isThirtyparcenton && !$isSuperAdmin) {
        // Sum for non-super admin with the thirty percent feature on
        return Cache::remember('sale_today_thirty_percent_'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')
                        ->where('date', '>=', $start_date)
                        ->where('date', '<=', $end_date)
                        ->where('vat_add', 1)
                        ->sum('total');
        });
    } else {
        // Sum for super admins and others
        return Cache::remember('sale_today_'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')
                        ->where('date', '>=', $start_date)
                        ->where('date', '<=', $end_date)
                        ->sum('total');
        });
    }
}

    public static function todaySaleCount_old($isSuperAdmin,$date)
    {
        // dd($isSuperAdmin);
        // die();
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($date);
        $start_date = Helpers::dayStartedAt($date);
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
        if ($isThirtyparcenton && !$isSuperAdmin){
            return Cache::remember('sale_count_today'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
                return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->where('vat_add',1)->count();
            });
        }else{
            return Cache::remember('sale_count_today'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
                return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->count();
            });
        }
  
    }
    public static function todaySaleCount($isSuperAdmin, $date)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($date);
        $start_date = Helpers::dayStartedAt($date);
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
    
        // Count for super admin
        // if ($isSuperAdmin) {
        //     return Cache::remember('sale_count_today_super_admin'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
        //         return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->count();
        //     });
        // }
    
        // Count for non-super admin with the thirty percent feature on
        if ($isThirtyparcenton && !$isSuperAdmin) {
            return Cache::remember('sale_count_today_thirty_percent'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
                return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->where('vat_add', 1)->count();
            });
        }else{
            return Cache::remember('sale_count_today_super_admin'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
                return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->count();
            });
        }
    
        // Default count
        return Cache::remember('sale_count_today'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->count();
        });
    }

    public static function LastMonthThisDaySale($isSuperAdmin, $date)
    { 
        $today = (clone $date);
    
        $lastYearMonthStart = $today->copy()->subYear()->startOfMonth();
        $lastYearMonthEnd = $today->copy()->subYear()->endOfMonth();
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($date);
        $start_date = Helpers::dayStartedAt($date);
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
            // 1. Last Month on This Day
        $lastMonthSameDay = $today->copy()->subMonthNoOverflow();
      

        return Cache::remember('sale_count_today_last_month'.$branch_id, self::$seconds, function () use ($lastMonthSameDay) {
            return Order::where('status', 'complete')
            ->whereDate('date', $lastMonthSameDay)
            ->sum('total');     
            });
    
    }
     //last year same day sale
     public static function LastYearToday($isSuperAdmin, $date)
     { 
         $today = (clone $date);
     
         $lastYearMonthStart = $today->copy()->subYear()->startOfMonth();
         $lastYearMonthEnd = $today->copy()->subYear()->endOfMonth();
         $branch_id = UseBranch::id();
         $end_date = Helpers::dayEndedAt($date);
         $start_date = Helpers::dayStartedAt($date);
         $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
         // 3. Last Year on This Day
            $lastYearSameDay = $today->copy()->subYear();
           

            return Cache::remember('sale_current_day_last_year'.$branch_id, self::$seconds, function () use ($lastYearSameDay) {
                return Order::where('status', 'complete')
                ->whereDate('date', $lastYearSameDay)
                ->sum('total');      
                });
     
     }
     //Yesterday sale till this time
     public static function yesterdaySaletillthistime($isSuperAdmin,$date)
     { 

       // dd($date);
       // die();
         $today = (clone $date);
         $tillthistime = Carbon::parse($date);

       //   $yesterdayEnd = Helpers::dayEndedAt($date)->copy()->subDay();
         $yesterdayStart =Helpers::dayStartedAt($date)->subDay();
         // Start of yesterday (beginning of the day)
         //$yesterdayStart = $today->copy()->subDay()->startOfDay();
     
         // End of yesterday (same time as now, but yesterday)
        // $yesterdayEnd = $tillthistime->copy()->subDay();
         
         // Get the current date and time in the Asia/Dhaka timezone
           $currentDateTime = Carbon::now(new DateTimeZone('Asia/Dhaka'));

           // Subtract one day to get this time yesterday
           $yesterdayEnd = $currentDateTime->copy()->subDay();

         //dd($yesterdayStart);
       //   dd($yesterdayEnd);
       //   die();
     
         $branch_id = UseBranch::id();
     
         // Cache key adjusted to include specific times
         $cacheKey = 'sale_until_exact_time_yesterday_'.$branch_id.'_'. $yesterdayStart->format('YmdHi').'_'.$yesterdayEnd->format('YmdHi');
     
         return Cache::remember($cacheKey, self::$seconds, function () use ($yesterdayStart, $yesterdayEnd) {
             return Order::where('status', 'complete')
                        ->where('date', '>=', $yesterdayStart)
                        ->where('date', '<=', $yesterdayEnd)
                        ->sum('total');      
         });
     }
   
   
      
    public static function yesterdaySale($date)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($date);
        $start_date = Helpers::dayStartedAt($date);

        return Cache::remember('sale_yesterday'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->sum('total');
        });
    }

    public static function yesterdaySaleCount($date)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($date);
        $start_date = Helpers::dayStartedAt($date);

        return Cache::remember('sale_count_yesterday'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->count();
        });
    }

    public static function currentMonthSale($month)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($month->endOfMonth());
        $start_date = Helpers::dayStartedAt($month->startOfMonth());
        // dd($start_date,$end_date);
        // die();
        return Cache::remember('sale_current_month'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->sum('total');
        });
    }


    public static function currentMonthAchive($month)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($month->endOfMonth());
        $start_date = Helpers::dayStartedAt($month->startOfMonth());

        return Cache::remember('sale_current_month'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->sum('total');
        });
    }

    public static function currentMonthSaleCount($month)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($month->endOfMonth());
        $start_date = Helpers::dayStartedAt($month->startOfMonth());

        return Cache::remember('sale_count_current_month'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->count();
        });
    }

    public static function LastMonthTillToday($isSuperAdmin, $date)
    { 
        $today = (clone $date);
        $branch_id = UseBranch::id();

       // 2. Last Month Till Today
        $lastMonthStart = $today->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthToday = $today->copy()->subMonthNoOverflow();
        // dd($lastMonthToday);
        // die();
       

        return Cache::remember('sale_lastmonth_till_today'.$branch_id, self::$seconds, function () use ($lastMonthStart, $lastMonthToday) {
            return Order::where('status', 'complete')
                                        ->whereBetween('date', [$lastMonthStart, $lastMonthToday])
                                        ->sum('total');       
            });                                
    
    }
    public static function LastYearTillToday($isSuperAdmin, $date)
    { 
        $today = (clone $date);
        $branch_id = UseBranch::id();

       // 4. Last Year on This Month
        //$lastYearMonthStart = $today->copy()->subYear()->startOfMonth();
        //$lastYearMonthEnd = $today->copy()->subYear()->endOfMonth();
        //$lastYearMonthEnd = $today->copy()->subYear()->endOfMonth()->setTime(23, 59, 59);
        $lastYearMonthStart = $today->copy()->subYear()->startOfMonth()->toDateString(); // '2023-12-01'
        $lastYearMonthEnd = $today->copy()->subYear()->endOfMonth()->toDateString();     // '2023-12-31'

        // dd([
        //     'lastYearMonthStart' => $lastYearMonthStart->toDateTimeString(),
        //     'lastYearMonthEnd' => $lastYearMonthEnd->toDateTimeString(),
        // ]);
        // dd($lastYearMonthEnd);
        // die();
        return Cache::remember('sale_lastyear_till_today'.$branch_id, self::$seconds, function () use ($lastYearMonthStart, $lastYearMonthEnd) {
            return Order::where('status', 'complete')
            ->whereBetween('date', [$lastYearMonthStart, $lastYearMonthEnd])
            ->sum('total');       
         });
    

    
    }

    public static function lastMonthSale_arif($month)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($month->endOfMonth());
        //$end_date = $month->endOfMonth(); //
        $start_date = Helpers::dayStartedAt($month->startOfMonth());
        //$start_date = $month->startOfMonth();

        

        // dd($start_date,$end_date);
        // die();
        // dd([
        //     'lastYearMonthStart' => $start_date->toDateTimeString(),
        //     'lastYearMonthEnd' => $end_date->toDateTimeString(),
        // ]);
        return Cache::remember('sale_last_month'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->sum('total');
        });
    }

    public static function lastMonthSale($month)
    {
        $branch_id = UseBranch::id();
    
        // Accurate start and end dates for December in Asia/Dhaka timezone
        $start_date = $month->copy()->startOfMonth()->format('Y-m-d 00:00:00'); // 2024-12-01 00:00:00
        $end_date = $month->copy()->endOfMonth()->format('Y-m-d 23:59:59'); // 2024-12-31 23:59:59
    // dd($start_date,$end_date);
    //     die();
        // Cache the results
        return Cache::remember('sale_last_month' . $branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')
                ->whereNull('deleted_at')
                ->whereBetween('date', [$start_date, $end_date])
                ->sum('total');
        });
    }
    

    public static function lastMonthSaleCount($month)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($month->endOfMonth());
        $start_date = Helpers::dayStartedAt($month->startOfMonth());

        return Cache::remember('sale_count_last_month'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Order::where('status', 'complete')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->count();
        });
    }

    public static function totalSale()
    {
        $branch_id = UseBranch::id();

        return Cache::remember('sale_total'.$branch_id, self::$seconds, function () {
            return Order::where('status', 'complete')->sum('total');
        });
    }

    public static function totalSaleCount()
    {
        $branch_id = UseBranch::id();

        return Cache::remember('sale_count'.$branch_id, self::$seconds, function () {
            return Order::where('status', 'complete')->count();
        });
    }

    public static function currentMonthExpense($month)
    {
        $branch_id = UseBranch::id();
        $end_date = (clone $month)->endOfMonth();
        $start_date = (clone $month)->startOfMonth();

        return Cache::remember('expense_current_month'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Expense::where('status', 'paid')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->sum('amount');
        });
    }

    public static function lastMonthExpense($month)
    {
        $branch_id = UseBranch::id();
        $end_date = (clone $month)->endOfMonth();
        $start_date = (clone $month)->startOfMonth();

        return Cache::remember('expense_last_month'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            return Expense::where('status', 'paid')->where('date', '>=', $start_date)->where('date', '<=', $end_date)->sum('amount');
        });
    }

    public static function hourly($date)
    {
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt($date);
        $start_date = Helpers::dayStartedAt($date);

        return Cache::remember('sale_last_24_hour'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            $data = Order::query()
                ->select(
                    DB::raw('DATE_FORMAT(date, "%h:%00 %p") as duration'),
                    DB::raw('SUM(total) as value'),
                )
				->where('status', 'complete')
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->groupBy('duration')
                ->get()
                ->pluck('value', 'duration');

            return Chart::line($data, $start_date, $end_date);
        });
    }

    public static function daily($date)
    {
        // Not fully configured with settings day_started_at field
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt();
        $start_date = Helpers::dayStartedAt($date);

        return Cache::remember('sale_last_30_day'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            $data = Order::query()
                ->select(
                    DB::raw('DATE_FORMAT(date, "%d %b, %y") as duration'),
                    DB::raw('SUM(total) as value'),
                )
				->where('status', 'complete')
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->groupBy('duration')
                ->get()
                ->pluck('value', 'duration');

            return Chart::column($data, $start_date, $end_date, 'day');
        });
    }

    public static function monthly_old($date)
    {
        // Not fully configured with settings day_started_at field
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt();
        $start_date = Helpers::dayStartedAt($date);
            // // Calculate the first day of the month, 5 months before the current month
            // $start_date = Carbon::parse($date)->startOfMonth();

            // // Use the current date as the end date
            // $end_date = Carbon::now();
        //dd($start_date);
        // dd($end_date);
        // die();

        return Cache::remember('sale_last_6_month'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            $data = Order::query()
                ->select(
                    DB::raw('DATE_FORMAT(date, "%b, %y") as duration'),
                    DB::raw('SUM(total) as value'),
                )
				->where('status', 'complete')
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->groupBy('duration')
                ->get()
                ->pluck('value', 'duration');

            return Chart::column($data, $start_date, $end_date, 'month');
        });
    }
    public static function monthly($date)
    {
        // Not fully configured with settings day_started_at field
        $branch_id = UseBranch::id();
        $end_date = Helpers::dayEndedAt();
        $start_date = Helpers::dayStartedAt($date);
            // // Calculate the first day of the month, 5 months before the current month
            // $start_date = Carbon::parse($date)->startOfMonth();

            // // Use the current date as the end date
            // $end_date = Carbon::now();
        //dd($start_date);
        // dd($end_date);
        // die();

        return Cache::remember('sale_last_6_month'.$branch_id, self::$seconds, function () use ($start_date, $end_date) {
            $data = Order::query()
                ->select(
                    DB::raw('DATE_FORMAT(date, "%b, %y") as duration'),
                    DB::raw('SUM(total) as value'),
                )
				->where('status', 'complete')
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->groupBy('duration')
                ->get()
                ->pluck('value', 'duration');

            return Chart::column($data, $start_date, $end_date, 'month');
        });
    }

    public static function monthly_new($date)
{
    $branch_id = UseBranch::id();
    
    // Calculate the first day of the month, 5 months before the current month
    $start_date = Carbon::parse($date)->startOfMonth();

    // Use the current date as the end date
    $end_date = Carbon::now();

    // dd($date);

    $cacheKey = 'sale_last_6_month' . $branch_id . '_' . $start_date->format('Ym') . '_' . $end_date->format('Ym');

    return Cache::remember($cacheKey, self::$seconds, function () use ($start_date, $end_date) {
        $data = Order::query()
            ->select(
                DB::raw('DATE_FORMAT(date, "%b, %y") as duration'),
                DB::raw('SUM(total) as value'),
            )
            ->where('status', 'complete')
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date)
            ->groupBy('duration')
            ->orderBy('date', 'asc') // Ensure the data is ordered correctly
            ->get()
            ->pluck('value', 'duration');

        return Chart::column($data, $start_date, $end_date, 'month');
    });
}
}
