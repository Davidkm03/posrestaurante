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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('preparation_started_at')->nullable()->after('scheduled_at');
            $table->timestamp('ready_at')->nullable()->after('preparation_started_at');
            $table->timestamp('served_at')->nullable()->after('ready_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['preparation_started_at', 'ready_at', 'served_at']);
        });
    }
};
