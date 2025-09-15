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
        // 1. Create product_inventories table
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('reference')->nullable();
            $table->string('serial');
            $table->enum('direction', ['in', 'out'])->default('in');

            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Add product_inventory_id to orders table
        if (!Schema::hasColumn('orders', 'product_inventory_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('product_inventory_id')->nullable()->constrained('product_inventories')->after('branch_id')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_inventories');
    }
};
