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
        Schema::table('payment_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_methods', 'bg_color')) {
                $table->string('bg_color')->default('#ffffff');
            }
            if (!Schema::hasColumn('payment_methods', 'text_color')) {
                $table->string('text_color')->default('#6b7280');
            }
            if (!Schema::hasColumn('payment_methods', 'text_size')) {
                $table->string('text_size')->default('14px');
            }
            if (!Schema::hasColumn('payment_methods', 'font_weight')) {
                $table->string('font_weight')->default('400');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
