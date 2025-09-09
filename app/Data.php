<?php

namespace App;

use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderPaymentMethod;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Scopes\UseBranchScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;

class Data
{
    public static $branchId;
    public static $startDate;
    public static $endDate;

    // Uneditable payment method for cash
    public static $METHOD_CASH = 1;

    public function __construct()
    {
    }

    public static function whereBranch($branchId)
    {
		self::$branchId = $branchId;
        return new self;
    }

    public static function whereBetween($start_date, $end_date)
    {
        self::$startDate = $start_date;
        self::$endDate = $end_date;

        return new self;
    }

	public static function orderObjects() 
	{
        return Order::withoutGlobalScope(UseBranchScope::class)
            ->when(self::$branchId, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->where('date', '>=', self::$startDate)
            ->where('date', '<=', self::$endDate)
            ->get();
	}
    public static function orderObjectsForVatAdd() 
	{
        return Order::withoutGlobalScope(UseBranchScope::class)
            ->when(self::$branchId, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->where('date', '>=', self::$startDate)
            ->where('date', '<=', self::$endDate)
            ->where('vat_add',1)
            ->get();
	}

	public static function expenseObjects() 
	{
        return Expense::withoutGlobalScope(UseBranchScope::class)
            ->when(self::$branchId, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->where('date', '>=', now()->parse(self::$startDate)->startOfDay())
            ->where('date', '<=', now()->parse(self::$endDate)->subDay()->endOfDay())
            ->get();
	}

	public static function orderIds() 
	{
        return self::orderObjects()->pluck('id');
        // $orders = self::orderObjects()->pluck('orders');
        // dd('Orders with ', $orders->toArray());
        // die();
        // return $orders->pluck('id');
    }
    public static function orderIdsForVatAdd() {
        // Assuming self::orderObjects() returns a query builder
        return self::orderObjects()
                   ->where('vat_add', 1)
                   ->pluck('id');
    }

    public static function pendingOrders()
    {
        $waiter_id = Auth::user()->is_waiter ? Auth::user()->id : null;

        return Order::withoutGlobalScope(UseBranchScope::class)
            ->select('id', 'invoice_number', 'status', 'is_printed', 'branch_id')
            ->when(self::$branchId, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->when(self::$startDate, function ($query, $start_date) {
                $query->where('date', '>=', $start_date);
            })
            ->when(self::$endDate, function ($query, $end_date) {
                $query->where('date', '<=', $end_date);
            })
            ->when($waiter_id, function ($query, $waiter_id) {
                $query->where('waiter_id', $waiter_id);
            })
            ->where('status', '!=', 'complete')
            ->orderBy('id', 'DESC')
            ->get();
    }

	public function summaryCards($isSuperAdmin)
    {
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
        $expenses = self::expenseObjects();
        $orders = self::orderObjects();

		$total_sale = $orders->where('status','complete')->sum('total');
        $total_vat_sale = $orders->sum('vat_amount');
        $total_expense = $expenses->where('status', 'paid')->sum('amount');
        $total_expense_due = $expenses->where('status', 'unpaid')->sum('amount');
        

        if($isThirtyparcenton && !$isSuperAdmin){
            $total_sale = $orders->where('vat_add', 1)->sum('total');
            $total_vat_sale = $orders->where('vat_add', 1)->sum('vat_amount');
            $total_expense = $expenses->where('status', 'paid' && 'vat_add',1)->sum('amount');
            $total_expense_due = $expenses->where('status', 'unpaid'  && 'vat_add',1)->sum('amount');
        }else{
            $total_sale = $orders->sum('total');
            $total_vat_sale = $orders->sum('vat_amount');
            $total_expense = $expenses->where('status', 'paid')->sum('amount');
            $total_expense_due = $expenses->where('status', 'unpaid')->sum('amount');
        }

        $total_sale_without_vat = $total_sale - $total_vat_sale;
        $total_income = $total_sale_without_vat - $total_expense;
        $total_guests_served = $orders->where('vat_add',1)->sum('guest_number');
        $avg_bucket_size = $total_sale / ($total_guests_served ? $total_guests_served : 1);

		$cards = [];
        $cards[] = [
            'name' => 'Total Sale',
            'amount' => Helpers::toAmount($total_sale),
        ];

        $cards[] = [
            'name' => 'Total VAT',
            'amount' => Helpers::toAmount($total_vat_sale),
        ];

        $cards[] = [
            'name' => 'Total Sale Without VAT',
            'amount' => Helpers::toAmount($total_sale_without_vat),
        ];

        $cards[] = [
            'name' => 'Total Expense',
            'amount' => Helpers::toAmount($total_expense),
        ];

        $cards[] = [
            'name' => 'Total Expense Due',
            'amount' => Helpers::toAmount($total_expense_due),
        ];

        $cards[] = [
            'name' => 'Total Income',
            'amount' => Helpers::toAmount($total_income),
        ];

        $cards[] = [
            'name' => 'Total Guests Served',
            'amount' => Helpers::toAmount($total_guests_served),
        ];

        $cards[] = [
            'name' => 'Average Bucket Size',
            'amount' => Helpers::toAmount($avg_bucket_size),
        ];

		return $cards;
    }

	public function recipeCards($isSuperAdmin)
    {
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');


        if ($isThirtyparcenton && !$isSuperAdmin){
            $order_ids = self::orderIdsForVatAdd();
            // var_dump($order_ids);
            // die();

        }else{
            $order_ids = self::orderIds()->where('vat_add',1);
            // var_dump($order_ids);
            // die();

        }
            $order_products = OrderProduct::select(DB::raw('SUM(quantity) as product_quantity'), 'product_id')
            ->whereIn('order_id', $order_ids)
            ->groupBy('product_id')
            ->pluck('product_quantity', 'product_id')->toArray();
      
        

        $total_sold = array_sum($order_products);

        $total_item_recipe_cost = Product::whereIn('id', array_keys($order_products))->get()->map(function ($product) use ($order_products) {
            return $product->production_cost * $order_products[$product->id];
        })->sum();

		$cards = [];

		$cards[] = [
            'name' => 'Total Item Recipe Cost',
            'amount' => Helpers::toAmount($total_item_recipe_cost),
        ];

        $cards[] = [
            'name' => 'Total Sold',
            'amount' => Helpers::toQuantity($total_sold),
        ];
		return $cards;
    }

	public function dineTypeCards($isSuperAdmin)
    {
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
        if ($isThirtyparcenton && !$isSuperAdmin){
            $orders = self::orderObjectsForVatAdd();
            // dd($orders);
            // die();

        }else{
            $orders = self::orderObjects();
            // dd($orders);
            // die();
        }

		return $orders->whereNotNull('dine_type')
            ->where('dine_type', '!=', '')
            ->groupBy('dine_type')
            ->mapWithKeys(function ($order, $key) {
                $total_sale = $order->sum('total');

                return [
                    $key => [
                        'name' => 'Total '.Str::of($key)->replace('-', ' ')->title()->value.' Sale',
                        'amount' => Helpers::toAmount($total_sale),
                    ],
                ];
            })->values()->all();
    }

	public function paymentMethodCards($isSuperAdmin)
    {
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
        if ($isThirtyparcenton && !$isSuperAdmin){
            $order_ids = self::orderIdsForVatAdd();

        }else{
            $order_ids = self::orderIds();

        }
		$cards = [];

		foreach (PaymentMethod::all() as $payment_method) {
            
            $sale_amount = OrderPaymentMethod::whereIn('order_id', $order_ids)->where('payment_method_id', $payment_method->id)->sum('amount');

            if ($payment_method->id == self::$METHOD_CASH) {
                $sale_amount -= Order::whereIn('id', $order_ids)->sum('change');
            }

            $cards[] = [
                'name' => 'Total '.$payment_method->name.' Sale',
                'amount' => Helpers::toAmount($sale_amount),
            ];
        }
		return $cards;
    }
    public static function hourly($value)
    {
        $end_time = Helpers::dayEndedAt();
        $current_time = Helpers::dayStartedAt();

        $data = [];
        do {
            $time = $current_time->format('h:i A');

            $data[] = [
                'dateFormat' => 'yyyy-MM-dd H:i',
                'xAxisName' => 'Hourly',
                'yAxisName' => 'Sales',
                'xAxisValue' => $current_time->format('Y-m-d h:i A'),
                'xAxisTooltip' => $current_time->format('H A'),
                'yAxisValue' => floatval($value[$time] ?? 0),
            ];

            $current_time = $current_time->addHour();

        } while ($current_time->lt($end_time));

        // dd($value, $data);
        return $data;
    }
}
