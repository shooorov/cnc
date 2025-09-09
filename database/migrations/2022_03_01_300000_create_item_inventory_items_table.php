<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->decimal('quantity', 8, 3)->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->enum('direction', ['in', 'out'])->default('in');

            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_inventory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_inventory_product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_inventory_items');
    }
};
