<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        $names = [
            'admin' => 'Admin',
            'manager' => 'Manager',
            // 'customer'  => 'Customer',
            // 'barista'   => 'Barista',
            // 'chef'      => 'Chef',
            // 'waiter'    => 'Waiter',
        ];

        foreach ($names as $short => $name) {
            DB::table('roles')->insert([
                'slug' => Str::replace(' ', '-', $name),
                'name' => $name,
                'short' => $short,
                'permissions' => null,
            ]);
        }
    }
}
