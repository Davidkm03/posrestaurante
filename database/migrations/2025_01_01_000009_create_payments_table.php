<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('restrict');
            $table->foreignId('cash_session_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('payment_method_id')->constrained()->onDelete('restrict');
            $table->string('status', 20)->default('completed');
            $table->decimal('amount', 12, 2);
            $table->decimal('received_amount', 12, 2)->nullable();
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->string('reference')->nullable();
            $table->string('authorization_code')->nullable();
            $table->json('gateway_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['order_id']);
            $table->index(['cash_session_id']);
        });

        Schema::create('tips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('waiter_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('type', 20)->default('voluntary'); // voluntary, suggested, service
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('authorized_by')->nullable()->constrained('users');
            $table->decimal('amount', 12, 2);
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('tips');
        Schema::dropIfExists('payments');
    }
};
