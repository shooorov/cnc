<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPaymentMethod;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DevController extends Controller
{
    public function test()
    {
    }

    public function order_payment_method()
    {
        foreach (Order::all() as $order) {
            if (empty($order->payment_method_id)) {
                $order->payment_method_id = PaymentMethod::first()->id;
                $order->save();
            }

            OrderPaymentMethod::create([
                'order_id' => $order->id,
                'payment_method_id' => $order->payment_method_id,
                'amount' => $order->cash,
            ]);
        }
    }

    public function status_update()
    {
        foreach (Order::all() as $order) {
            if (empty($order->cash)) {
                $order->cash = $order->total;
            }

            if ($order->cash >= $order->total) {
                $order->status = 'complete';
            }

            if ($order->payment_method_id != 1) {
                $order->status = 'complete';
            }
            $order->save();
        }
        // foreach(PurchaseOrder::all() as $po) {
        //     $array = explode('-', $po->job_serial);
        //     $po->job_serial = count($array) > 1 ? $array[1] : null;
        //     $po->save();
        // }
    }

    public function eCommerce()
    {
        $category = ProductCategory::updateOrCreate([
            'name' => 'eCommerce',
        ]);

        foreach (Product::all() as $product) {
            Product::updateOrCreate([
                'name' => $product->name,
                'product_category_id' => $category->id,
            ], [
                'code' => "eC-$product->code",
                'english_name' => $product->english_name,
                'rate' => $product->rate,
                'discount' => $product->discount,
                'production_cost' => $product->production_cost,
                'margin_percent' => $product->margin_percent,
                'margin_amount' => $product->margin_amount,
                'vat_applicable' => $product->vat_applicable,
                'description' => $product->description,
                'number_of_persons' => $product->number_of_persons,
                'image' => $product->image,
            ]);
        }
    }

    public function command()
    {
    }

    public function seed(HttpRequest $request)
    {
        $params = [
            'table' => $request->table,
            'fields_to_skip' => ['id', 'created_at', 'updated_at', 'deleted_at'],
        ];

        return view('seed', $params);
    }

    public function resetProduct()
    {
        // Product::query()->update(['balance' => 0]);
        // echo 'Products balance reset to 0' . '<br>';
        // $quality_checks = QualityCheck::all()->filter(function ($qc, $key) {
        //     return Helpers::hasClient($qc);
        // });

        // foreach ($quality_checks as $qc) {
        //     $product = optional(optional($qc->lot)->job)->product;
        //     if ($product) {
        //         $balance = $product->balance + $qc->approved;
        //         $product->update(['balance' => $balance]);
        //     }
        // }
        // echo 'Products balance adjusted after QC' . '<br>';

        // $deliveries = Delivery::all()->filter(function ($delivery, $key) {
        //     return Helpers::hasClient($delivery);
        // });

        // foreach ($deliveries as $delivery) {
        //     foreach ($delivery->products as $del_product) {
        //         $product = $del_product->product;
        //         if ($product) {
        //             $balance = $product->balance - $del_product->quantity;
        //             $product->update(['balance' => $balance]);
        //         }
        //     }
        // }

        echo 'Products balance adjusted after Delivery'.'<br>';
        echo 'Completed'.'<br>';
    }

    public function seeder()
    {
        $params = [
            'products' => Product::all(),
        ];

        return view('seeder', $params);
    }

    public function relation()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $tables_list = [];
        foreach ($tables as $table) {
            $table = Str::studly(Str::singular($table));
            if ($this->dummy_model($table)) {
                array_push($tables_list, $table);
            }
        }

        $tables = [];
        foreach ($tables_list as $table) {
            $tables[$table] = [
                'columns' => [],
                'relations' => [],
            ];
        }
        foreach ($tables_list as $table) {
            $model = 'App\Models\\'.$table;
            $model = new $model;
            $table_columns = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
            $remove_columns = ['created_at', 'updated_at', 'deleted_at'];
            foreach ($remove_columns as $remove_column) {
                if (($key = array_search($remove_column, $table_columns)) !== false) {
                    unset($table_columns[$key]);
                }
            }
            $tables[$table]['columns'] = $table_columns;
            foreach ($table_columns as $column) {
                $column = str_replace('_id', '', $column);
                $column = ucfirst($column);
                if (array_key_exists($column, $tables) || in_array($column, array_keys($tables))) {
                    array_push($tables[$column]['relations'], $table);
                }
            }
        }

        $string = '<div style=\'margin: auto; width: 55%;\'> <h1>List of model in models</h1> <table style=\'margin: auto;\'><tr>';
        foreach ($tables as $key => $table) {
            if (count($table['relations'])) {
                $string .= '<th>'.$key.'</th>';
                $string .= '<td>'.str_repeat('&nbsp;', 15).implode(', ', $table['relations']).'</td></tr>';
            }
        }
        $string .= '</table></div>';
        echo $string;
    }

    public function dummy_model(string $string)
    {
        return ! in_array($string, ['CheckStep', 'FailedJob', 'Migration', 'Notification', 'PasswordReset', 'Salary', 'SignStep', 'StepUser', 'Status', 'Signature', 'Setting', 'Address', 'Image', 'Document', 'Remark', 'PersonalAccessToken', 'ProductTopping']);
    }
}
