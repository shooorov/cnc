<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Item;
use App\Models\OtherSale;
use App\Models\Product;
use App\Models\Remark;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class NeutralDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neutral:database {--developer=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'database neutralization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $developer = $this->option('developer');
        if ($developer != 'Ar1f') {
            echo "Developer not found!\n";

            return;
        }

        foreach (Branch::all() as $item) {
            echo "{$item->name}\n";
            $item->name = "Branch $item->id";
            $item->short_code = "branch-$item->id";
            $item->address = "Branch $item->id Address";
            $item->token_value = rand(3, 7);
            $item->pos_end_line = 'THANK YOU!';
            $item->save();
        }

        foreach (Account::all() as $item) {
            echo "{$item->name}\n";
            $item->name = "Account $item->id ($item->type)";
            $item->number = Str::random(16);
            $item->branch = "Account $item->id Branch";
            $item->address = "Account $item->id Address";
            $item->save();
        }

        foreach (Product::all() as $item) {
            echo "{$item->name}\n";
            $item->name = "Menu Item $item->id";
            $item->save();
        }

        foreach (Item::all() as $item) {
            echo "{$item->name}\n";
            $item->name = "Ingredient $item->id";
            $item->save();
        }

        foreach (User::all() as $item) {

            echo "{$item->name}\n";
            if ($employee = Employee::where('email', $item->email)->first()) {
                $employee->name = "User $item->id";
                $employee->email = "user$item->id@gmail.com";
                $employee->save();
            }

            $item->name = "User $item->id";
            $item->email = "user$item->id@gmail.com";
            $item->password = bcrypt("user$item->id@gmail.com");
            $item->save();
        }

        OtherSale::chunk(50, function ($items) {
            foreach ($items as $item) {
                echo "{$item->note}\n";
                $item->note = 'Note '.$item->id;
                $item->save();
            }
        });

        foreach (Product::all() as $item) {
            echo "{$item->name}\n";
            $item->name = "Menu Item $item->id";
            $item->save();
        }

        foreach (ExpenseCategory::all() as $item) {
            echo "{$item->name}\n";
            $item->name = "Expense Category $item->id";
            $item->save();
        }

        Expense::chunk(50, function ($items) {
            foreach ($items as $item) {
                echo "{$item->note}\n";
                $item->note = 'Note '.$item->id;
                $item->save();
            }
        });

        // Transaction::chunk(50, function ($items) {
        //     foreach ($items as $item) {
        //         echo "{$item->note}\n";
        //         $item->note = "Note " . $item->id;
        //         $item->save();
        //     }
        // });

        foreach (Remark::all() as $item) {
            echo "{$item->remark}\n";
            $item->remark = "Remark $item->id";
            $item->save();
        }

        $settings = [
            'item_inventory' => [
                'type' => 'rename',
                'title' => 'Item Inventory',
                'value' => 'Inventory',
            ],
            'product_inventory' => [
                'type' => 'rename',
                'title' => 'Product Inventory',
                'value' => 'Menu Inventory',
            ],
            'item' => [
                'type' => 'rename',
                'title' => 'Item',
                'value' => 'Ingredient',
            ],
            'product' => [
                'type' => 'rename',
                'title' => 'Product',
                'value' => 'Menu Item',
            ],

            // Basic Setting for Company
            'company_name' => [
                'type' => 'text',
                'title' => 'Restaurant Name',
                'value' => 'Coal & Coffee',
            ],
            'company_address' => [
                'type' => 'textarea',
                'title' => 'Restaurant Address',
                'value' => 'House no.60, Sonargaon janapath road, Sector 9 uttara',
            ],
            'bin_number' => [
                'type' => 'textarea',
                'title' => 'Bin Number',
                'value' => 'BIN: 003649774-0101',
            ],
            'day_started_at' => [
                'type' => 'text',
                'title' => 'Day Started At',
                'value' => '00:00',
            ],

        ];

        Setting::truncate();
        foreach ($settings as $setting) {
            Setting::updateOrCreate([
                'name' => $setting['name'],
                'type' => $setting['type'],
            ], [
                'title' => $setting['title'],
                'value' => $setting['value'] ?? '',
                'created_at' => now(),
            ]);
        }
    }
}
