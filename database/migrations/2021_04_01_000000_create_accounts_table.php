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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['cash', 'bank', 'petty-cash'])->default('bank');
            $table->string('name');
            $table->string('number')->unique()->nullable();
            $table->string('branch')->nullable();
            $table->longText('address')->nullable();
            $table->decimal('opening_balance', 16, 2)->nullable();
            $table->datetime('opening_date')->nullable();
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
        Schema::dropIfExists('accounts');
    }
};
