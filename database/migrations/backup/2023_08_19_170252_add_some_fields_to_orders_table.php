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
            // Add new columns with proper placement
            if (!Schema::hasColumn('orders', 'is_deliverable')) {
                $table->boolean('is_deliverable')->default(false)->after('status');
            }
            if (!Schema::hasColumn('orders', 'table_name')) {
                $table->string('table_name')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'invoice_number')) {
                $table->string('invoice_number')->after('number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
