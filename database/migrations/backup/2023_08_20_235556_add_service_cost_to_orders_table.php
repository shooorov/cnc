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
            // Add service_cost column if it doesn't exist
            if (!Schema::hasColumn('orders', 'service_cost')) {
                $table->decimal('service_cost', 10, 2)->default(0)->after('delivery_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
