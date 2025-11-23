<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('color', 7)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('number', 20);
            $table->string('name')->nullable();
            $table->integer('capacity')->default(4);
            $table->string('status', 20)->default('free');
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->string('shape', 20)->default('square');
            $table->integer('width')->default(100);
            $table->integer('height')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamp('occupied_at')->nullable();
            $table->foreignId('current_order_id')->nullable();
            $table->foreignId('assigned_waiter_id')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'number']);
            $table->index(['zone_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
        Schema::dropIfExists('zones');
    }
};
