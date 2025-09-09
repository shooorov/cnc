<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sale_targets')) {
            Schema::create('sale_targets', function (Blueprint $table) {
                $table->id();
                $table->integer('branch_id');
                $table->decimal('target_amount', 10, 2);
                $table->decimal('achived_amount', 10, 2)->nullable();
                $table->decimal('deficit_amount', 10, 2)->nullable();
                $table->string('target_month', 50)->nullable();
                $table->year('target_year')->nullable();
                $table->integer('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_targets');
    }
};
