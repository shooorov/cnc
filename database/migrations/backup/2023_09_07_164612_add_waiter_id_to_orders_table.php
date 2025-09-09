<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'waiter_id')) {
                $table->foreignId('waiter_id')
                    ->nullable()
                    ->constrained('users')
                    ->cascadeOnDelete()
                    ->after('employee_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
