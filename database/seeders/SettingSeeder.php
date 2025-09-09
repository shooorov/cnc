<?php

namespace Database\Seeders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            // Menu Text Change by Company
            // Do not change the order(rename may not work properly)
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
            'token_value' => [
                'type' => 'text',
                'title' => 'Token Value',
                'value' => '0',
            ],
        ];

        Schema::dropIfExists('settings');

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable()->comment('all html input type');
            $table->string('name');
            $table->string('title');
            $table->string('value')->default(0);
            $table->timestamps();
        });

        DB::table('settings')->truncate();

        foreach ($settings as $name => $setting) {
            DB::table('settings')->insert([
                'type' => $setting['type'] ?? 'text',
                'name' => $name,
                'title' => $setting['title'],
                'value' => $setting['value'],
            ]);
        }
        Artisan::call('optimize:clear');
    }
}
