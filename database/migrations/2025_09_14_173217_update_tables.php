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
        Schema::table('kitchen_delivery_items', function (Blueprint $table) {
            if (!Schema::hasColumn('kitchen_delivery_items', 'rate')) {
                $table->decimal('rate', 16, 2)->nullable()->after('delivery_quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kitchen_delivery_items', function (Blueprint $table) {
            if (Schema::hasColumn('kitchen_delivery_items', 'rate')) {
                $table->dropColumn('rate');
            }
        });
    }
};
