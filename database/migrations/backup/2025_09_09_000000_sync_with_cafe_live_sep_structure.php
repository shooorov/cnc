<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration syncs the current database structure with cafe_live_sep_structure.sql
     */
    public function up(): void
    {
        // ===========================================
        // BRANCHES TABLE - Add missing columns
        // ===========================================
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'service_cost')) {
                $table->decimal('service_cost', 10, 2)->default(0.00)->after('delivery_cost');
            }
            if (!Schema::hasColumn('branches', 'sale_target')) {
                $table->decimal('sale_target', 10, 2)->nullable()->after('pos_end_line');
            }
            if (!Schema::hasColumn('branches', 'status')) {
                $table->string('status', 255)->default('active')->after('chef_target');
            }
        });

        // ===========================================
        // CUSTOMERS TABLE - Add missing token column
        // ===========================================
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'token')) {
                $table->decimal('token', 14, 2)->default(0.00)->after('is_subscribed');
            }
        });

        // ===========================================
        // ORDERS TABLE - Add missing columns and fix foreign keys
        // ===========================================
        Schema::table('orders', function (Blueprint $table) {
            // Add missing columns from SQL structure
            if (!Schema::hasColumn('orders', 'order_count')) {
                $table->integer('order_count')->nullable()->after('number');
            }
            if (!Schema::hasColumn('orders', 'invoice_number')) {
                $table->string('invoice_number', 191)->after('order_count');
            }
            if (!Schema::hasColumn('orders', 'service_cost')) {
                $table->decimal('service_cost', 10, 2)->default(0.00)->after('delivery_cost');
            }
            if (!Schema::hasColumn('orders', 'token')) {
                $table->decimal('token', 10, 2)->default(0.00)->after('cash');
            }
            if (!Schema::hasColumn('orders', 'member_code')) {
                $table->integer('member_code')->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('orders', 'member_name')) {
                $table->string('member_name', 120)->nullable()->after('member_code');
            }
            if (!Schema::hasColumn('orders', 'member_expired')) {
                $table->datetime('member_expired')->nullable()->after('member_name');
            }
            if (!Schema::hasColumn('orders', 'member_discount')) {
                $table->string('member_discount', 120)->nullable()->after('member_expired');
            }
            if (!Schema::hasColumn('orders', 'vat_add')) {
                $table->integer('vat_add')->default(0)->after('vat_amount');
            }
            if (!Schema::hasColumn('orders', 'table_name')) {
                $table->string('table_name', 191)->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'is_deliverable')) {
                $table->boolean('is_deliverable')->default(false)->after('table_name');
            }
            if (!Schema::hasColumn('orders', 'is_printed')) {
                $table->boolean('is_printed')->default(false)->after('is_deliverable');
            }

            // Updated foreign keys using latest Laravel style
            if (!Schema::hasColumn('orders', 'stuff_id')) {
                $table->foreignId('stuff_id')->nullable()->constrained('stuffs')->after('history');
            }
            if (!Schema::hasColumn('orders', 'payment_method_id')) {
                $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->after('stuff_id');
            }
            if (!Schema::hasColumn('orders', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->constrained('employees')->after('payment_method_id');
            }
            if (!Schema::hasColumn('orders', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->after('employee_id');
            }
            if (!Schema::hasColumn('orders', 'creator_id')) {
                $table->foreignId('creator_id')->nullable()->constrained('users')->after('customer_id');
            }
            if (!Schema::hasColumn('orders', 'product_inventory_id')) {
                $table->foreignId('product_inventory_id')->nullable()->constrained('product_inventories')->after('branch_id');
            }
            if (!Schema::hasColumn('orders', 'manager_id')) {
                $table->foreignId('manager_id')->nullable()->constrained('users')->after('product_inventory_id');
            }
            if (!Schema::hasColumn('orders', 'waiter_id')) {
                $table->foreignId('waiter_id')->nullable()->constrained('users')->after('manager_id');
            }
        });

        // ===========================================
        // PRODUCTS TABLE - Add missing status column
        // ===========================================
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'status')) {
                $table->string('status', 191)->default('active')->after('image');
            }
        });

        // ===========================================
        // CREATE MISSING TABLES
        // ===========================================

        // Create sale_targets table
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

        // Additional supporting tables
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key', 191)->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key', 191)->primary();
                $table->string('owner', 191);
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email', 191)->index();
                $table->string('token', 191);
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email', 255)->primary();
                $table->string('token', 255);
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('migrations')) {
            Schema::create('migrations', function (Blueprint $table) {
                $table->id();
                $table->string('migration', 191);
                $table->integer('batch');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
