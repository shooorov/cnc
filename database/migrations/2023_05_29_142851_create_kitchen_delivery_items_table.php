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
        Schema::create('kitchen_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->integer('requisition_quantity')->nullable();
            $table->integer('delivery_quantity')->nullable();
            $table->decimal('avg_rate', 16, 2)->nullable();
            $table->decimal('total', 9, 2)->nullable();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kitchen_delivery_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_delivery_items');
    }
};
