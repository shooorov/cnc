<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            // SettingSeeder::class,
            // RoleSeeder::class,
            // PaymentMethodSeeder::class,
            // DesignationSeeder::class,
            // ItemSeeder::class,
            // ProductSeeder::class,

            UserSeeder::class,
            // EmployeeSeeder::class,

            // UnitSeeder::class,
            // ExpenseCategorySeeder::class,
        ]);

        Artisan::call('optimize:clear');

    }
}
