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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->decimal('gross', 16, 2)->nullable();
            $table->decimal('basic', 14, 2)->nullable();
            $table->decimal('rent', 14, 2)->nullable();
            $table->decimal('medical', 14, 2)->nullable();
            $table->decimal('transport', 14, 2)->nullable();
            $table->decimal('food', 14, 2)->nullable();
            $table->decimal('other', 14, 2)->nullable();
            $table->integer('bonus_rate')->nullable();
            $table->decimal('bonus', 16, 2)->nullable();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('salaries');
    }
};
