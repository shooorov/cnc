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
        Schema::create('order_product_employees', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->foreignId('order_id')->cascadeOnDelete();
            $table->foreignId('order_product_id')->cascadeOnDelete();
            $table->foreignId('employee_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_employees');
    }
};
