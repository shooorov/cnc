<?php

use App\Models\Expense;
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
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'status')) {
                // $table->enum('status', array_keys((new Expense)->statuses))->default('unpaid')->after('amount');
                $table->string('status')->default('unpaid')->after('amount');
            }
        });

        Expense::where('status', 'unpaid')->update([
            'status' => 'paid',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
