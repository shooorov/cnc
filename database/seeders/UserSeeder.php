<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Arif Hasan',
            'password' => bcrypt('admin@example.com'),
            'status' => 'active',
            'is_admin' => true,
            'created_at' => now(),
            'email_verified_at' => now(),
        ]);
    }
}
