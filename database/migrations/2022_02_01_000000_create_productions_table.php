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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('chef or barista or etc');
            $table->string('status')->default('pending');
            $table->integer('duration')->nullable()->default(0);
            $table->integer('quantity')->nullable()->default(0);

            $table->datetime('viewed_at')->nullable();
            $table->datetime('accepted_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->datetime('delivered_at')->nullable();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kitchen_log_id')->nullable()->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('productions');
    }
};
