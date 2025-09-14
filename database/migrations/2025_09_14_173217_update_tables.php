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
        Schema::table('product_requisition_items', function (Blueprint $table) {
            if (!Schema::hasColumn('product_requisition_items', 'rate')) {
                $table->decimal('rate', 16, 2)->nullable();
            }
        });

        Schema::table('requisition_items', function (Blueprint $table) {
            if (!Schema::hasColumn('requisition_items', 'rate')) {
                $table->decimal('rate', 16, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
