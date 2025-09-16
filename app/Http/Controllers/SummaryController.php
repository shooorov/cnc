<?php

namespace App\Http\Controllers;

use App\Chart;
use App\Data;
use App\Helpers;
use App\Http\Cache\CacheItemCategory;
use App\Http\Cache\CacheProductCategory;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use App\RolePermission;
use App\UseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log; // Import Log facade

class SummaryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'access.permission']);
    }

    /**
     * Show the application data.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function overview(Request $request)
    {
        $isDateSearch = RolePermission::isEnabled('record_search.reporting_summery_date_search');
        $isSuperAdmin = $request->user()->is_admin;
        if ($isDateSearch) {
            $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : Helpers::dayEndedAt();
            $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : Helpers::dayStartedAt();
        } else {
            $end_date = Helpers::dayEndedAt();
            $start_date = Helpers::dayStartedAt();
        }

		$cards_summary = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->summaryCards($isSuperAdmin);
		$cards_payment_method = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->paymentMethodCards($isSuperAdmin);
		$cards_dine_type = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->dineTypeCards($isSuperAdmin);
		$cards_recipe = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->recipeCards($isSuperAdmin);

		$cards = array_merge(
			$cards_summary,
			[[]],
			$cards_recipe,
			[[]],
			$cards_dine_type,
			[[]],
			$cards_payment_method,
		);

		$params = [
            'cards' => $cards,
            'filter' => [
                'end_date' => Helpers::operationDay($end_date),
                'start_date' => Helpers::operationDay($start_date),
            ],
        ];
        // dd($params);
        // die();

        return Inertia::render('Reporting/Summery', $params);
    }

    /**
     * Show the Product list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function product(Request $request)
    {
        $branch_id = UseBranch::id();
        $category_id = $request->category_id;

        $isDateSearch = RolePermission::isEnabled('record_search.reporting_product_date_search');
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
        $isSuperAdmin = $request->user()->is_admin;

        if ($isDateSearch) {
            $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : Helpers::dayEndedAt();
            $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : Helpers::dayStartedAt();
        } else {
            $end_date = Helpers::dayEndedAt();
            $start_date = Helpers::dayStartedAt();
        }

        $sold_products = DB::table('orders')->select(
            'product_id',
            'products.name',
            'products.rate',
            DB::raw('SUM(order_products.quantity) as product_quantity'),
            DB::raw('SUM(order_products.quantity * order_products.rate) as product_amount')
            // DB::raw('FORMAT(SUM(order_products.quantity), 0) as product_quantity'),
            // DB::raw('FORMAT(SUM(order_products.quantity * order_products.rate), 2) as product_amount')
        )
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->join('product_categories', 'product_categories.id', '=', 'products.product_category_id')
            ->when($start_date, function ($query, $start_date) {
                $query->where('orders.date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->where('orders.date', '<=', $end_date);
            })
            ->when($category_id, function ($query, $category_id) {
                $query->where('products.product_category_id', $category_id);
            })
            ->when($branch_id, function ($query, $branch_id) {
                $query->where('orders.branch_id', $branch_id);
            })
            ->when($isThirtyparcenton && !$isSuperAdmin, function ($query) {
                $query->where('orders.vat_add', 1);
            })
            ->whereNull('orders.deleted_at')
            ->groupBy('product_id', 'products.name', 'products.rate')
            ->orderBy('products.name')
            ->get();

        $params = [
            'sold_products' => $sold_products,
            'categories' => CacheProductCategory::get()->whereNull('product_category_id')->values(),

            'filter' => [
                'category_id' => $category_id,
                'end_date' => Helpers::operationDay($end_date),
                'start_date' => Helpers::operationDay($start_date),
            ],
        ];

        return Inertia::render('Reporting/Product', $params);
    }

    /**
     * Show the Product list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function item(Request $request)
    {
        $branch_id = UseBranch::id();
        $category_id = $request->category_id;

        $isDateSearch = RolePermission::isEnabled('record_search.reporting_item_date_search');
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');
        $isSuperAdmin = $request->user()->is_admin;
        if ($isDateSearch) {
            $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : Helpers::dayEndedAt();
            $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : Helpers::dayStartedAt();
        } else {
            $end_date = Helpers::dayEndedAt();
            $start_date = Helpers::dayStartedAt();
        }

        $sold_products = DB::table('orders')->select(
            'product_id',
            'products.name',
            'products.rate',
            DB::raw('SUM(order_products.quantity) as product_quantity'),
            DB::raw('SUM(order_products.quantity * order_products.rate) as product_amount')
            // DB::raw('FORMAT(SUM(order_products.quantity), 0) as product_quantity'),
            // DB::raw('FORMAT(SUM(order_products.quantity * order_products.rate), 2) as product_amount')
        )
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->join('product_categories', 'product_categories.id', '=', 'products.product_category_id')
            ->when($start_date, function ($query, $start_date) {
                $query->where('orders.date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->where('orders.date', '<=', $end_date);
            })
            ->when($category_id, function ($query, $category_id) {
                $query->where('products.product_category_id', $category_id);
            })
            ->when($branch_id, function ($query, $branch_id) {
                $query->where('orders.branch_id', $branch_id);
            })
            ->when($isThirtyparcenton && !$isSuperAdmin, function ($query) {
                $query->where('orders.vat_add', 1);
            })
            ->whereNull('orders.deleted_at')
            ->groupBy('product_id', 'products.name', 'products.rate')
            ->orderBy('products.name')
            ->get();

        $items = Item::all();

        foreach ($sold_products as $sold_product) {
            $product = Product::find($sold_product->product_id);

            foreach ($product->items as $product_item) {
                $item = $items->first(fn ($i) => $i->id == $product_item->item_id);
                $item->quantity = ($item->quantity ?? 0) + $product_item->quantity;
            }
        }

        $params = [
            'items' => $items->filter(fn ($i) => $i->quantity > 0)->toArray(),
            'categories' => CacheItemCategory::get()->whereNull('item_category_id')->values(),

            'filter' => [
                'category_id' => $category_id,
                'end_date' => Helpers::operationDay($end_date),
                'start_date' => Helpers::operationDay($start_date),
            ],
        ];

        return Inertia::render('Reporting/Item', $params);
    }

    /**
     * Show the application data.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function hourly(Request $request)
    {
        // var_dump($request->start_date);
        // var_dump($request->end_date);
        // die();
        $isDateSearch = RolePermission::isEnabled('record_search.reporting_hourly_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : Helpers::dayEndedAt();
            $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : Helpers::dayStartedAt();
        } else {
            $end_date = Helpers::dayEndedAt();
            $start_date = Helpers::dayStartedAt();
        }

        $records = Order::select(
            DB::raw('DATE_FORMAT(date, "%h:%00 %p") as duration'),
            DB::raw('SUM(total) as total'),
        )
            ->when($start_date, function ($query, $start_date) {
                $query->where('date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->where('date', '<=', $end_date);
            })
            ->groupBy('duration')
            ->get()
            ->pluck('total', 'duration');

        Log::info($records);

        $data = Chart::line($records, Helpers::dayStartedAt(), Helpers::dayEndedAt());
        Log::info($data);

        $params = [
            'data' => $data,
            'filter' => [
                'end_date' => Helpers::operationDay($end_date),
                'start_date' => Helpers::operationDay($start_date),
            ],
        ];
        Log::info($params);


        return Inertia::render('Reporting/Hourly', $params);
    }

    /**
     * Show the application data.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function hourly2(Request $request)
    {
        $isDateSearch = RolePermission::isEnabled('record_search.reporting_hourly_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : Helpers::dayEndedAt();
            $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : Helpers::dayStartedAt();
        } else {
            $end_date = Helpers::dayEndedAt();
            $start_date = Helpers::dayStartedAt();
        }

        $records = Order::select(
            DB::raw('DATE_FORMAT(date, "%h:%00 %p") as duration'),
            DB::raw('SUM(total) as total'),
        )
            ->when($start_date, function ($query, $start_date) {
                $query->where('date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->where('date', '<=', $end_date);
            })
            ->groupBy('duration')
            ->get()
            ->pluck('total', 'duration');

            // var_dump($records);
            // die();

        $data = Chart::line($records, Helpers::dayStartedAt(), Helpers::dayEndedAt());
        // var_dump($start_date);
        // var_dump($end_date);
        // die();
        Log::info($data);

        $params = [
            'data' => $data,
            'filter' => [
                'end_date' => Helpers::operationDay($end_date),
                'start_date' => Helpers::operationDay($start_date),
            ],
        ];

        return Inertia::render('Reporting/Hourly', $params);
    }

    // /**
    //  * Show the application data.
    //  *
    //  * @return \Illuminate\Contracts\Support\Renderable
    //  */
    // public function hourly(Request $request)
    // {
    //     $isDateSearch = RolePermission::isEnabled('record_search.reporting_hourly_date_search');

    //     if ($isDateSearch) {
    //         $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : Helpers::dayEndedAt();
    //         $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : Helpers::dayStartedAt();
    //     } else {
    //         $end_date = Helpers::dayEndedAt();
    //         $start_date = Helpers::dayStartedAt();
    //     }

    //     $records = Order::join('branches', 'branches.id', '=', 'orders.branch_id')
    //         ->select(
    //             DB::raw('DATE_FORMAT(date, "%h:%00 %p") as duration'),
    //             DB::raw('SUM(total) as total'),
    //         )
    //         ->when($start_date, function ($query, $start_date) {
    //             $query->where('date', '>=', $start_date);
    //         })
    //         ->when($end_date, function ($query, $end_date) {
    //             $query->where('date', '<=', $end_date);
    //         })
    //         ->groupBy('duration')
    //         ->get();

    //     $data = Data::hourly($records->pluck('total', 'duration'));

    //     // $data = ChartLine::collection($values)->values();
    //     // dd($data);

    //     $params = [
    //         'data' => $data,
    //         'filter' => [
    //             'end_date' => Helpers::operationDay($end_date),
    //             'start_date' => Helpers::operationDay($start_date),
    //         ],
    //     ];

    //     return Inertia::render('Reporting/Hourly', $params);
    // }
}
