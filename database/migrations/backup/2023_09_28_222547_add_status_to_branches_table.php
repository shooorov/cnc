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
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'status')) {
                $table->string('status')->default('active')->after('chef_target');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
