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
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
            $table->string('mobile')->nullable();
            $table->string('status')->default('active');
            $table->longText('address')->nullable();
            $table->string('image')->nullable();

            $table->boolean('is_admin')->default(0);
            $table->boolean('is_waiter')->default(0);
            $table->boolean('is_barista')->default(0);
            $table->boolean('is_chef')->default(0);
            $table->boolean('is_rider')->default(0);

            $table->foreignId('role_id')->nullable()->constrained();
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['slug', 'mobile', 'address', 'is_admin']);
            $table->dropForeign(['role_id']);
        });
    }
};
