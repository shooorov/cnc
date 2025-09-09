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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('short_code', 191);
            $table->string('address', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->decimal('vat_rate', 8, 2)->nullable();
            $table->decimal('delivery_cost', 8, 2)->nullable();
            $table->decimal('service_cost', 10, 2)->default(0.00);
            $table->string('opening_hours', 191)->nullable();
            $table->longText('pos_end_line')->nullable();
            $table->decimal('sale_target', 10, 2)->nullable();
            $table->string('barista_target', 191)->nullable();
            $table->string('chef_target', 191)->nullable();
            $table->string('status', 255)->default('active');
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
        Schema::dropIfExists('branches');
    }
};
