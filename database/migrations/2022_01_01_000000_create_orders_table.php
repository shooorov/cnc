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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('number', 191);
            $table->integer('order_count')->nullable();
            $table->string('invoice_number', 191);
            $table->enum('type', ['self', 'delivery'])->default('self');
            $table->decimal('sub_total', 8, 2)->nullable();
            $table->decimal('delivery_cost', 8, 2)->nullable();
            $table->decimal('service_cost', 10, 2)->default(0.00);
            $table->decimal('total', 8, 2)->nullable();
            $table->decimal('cash', 8, 2)->nullable();
            $table->decimal('token', 10, 2)->default(0.00);
            $table->decimal('change', 8, 2)->nullable();

            $table->enum('discount_type', ['flat', 'percent'])->default('flat');
            $table->decimal('discount_rate', 8, 2)->nullable()->comment('in percent');
            $table->decimal('discount_amount', 8, 2)->nullable();
            $table->integer('member_code')->nullable();
            $table->string('member_name', 120)->nullable();
            $table->datetime('member_expired')->nullable();
            $table->string('member_discount', 120)->nullable();

            $table->decimal('vat_rate', 8, 2)->nullable();
            $table->decimal('vatable_amount', 8, 2)->nullable()->comment('after removing the value of non taxable items.');
            $table->decimal('vat_amount', 8, 2)->nullable();
            $table->integer('vat_add')->default(0);

            $table->string('dine_type', 191);
            $table->text('delivery_address')->nullable();
            $table->integer('guest_number')->nullable();
            $table->string('note', 191)->nullable();
            $table->string('status', 191)->default('pending');
            $table->string('table_name', 191)->nullable();
            $table->boolean('is_deliverable')->default(false);
            $table->boolean('is_printed')->default(false);
            $table->longText('history')->nullable();

            $table->foreignId('stuff_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('waiter_id')->nullable()->constrained('users')->cascadeOnDelete();
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
        Schema::dropIfExists('orders');
    }
};
