<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('restrict');
            $table->foreignId('table_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // Mesero/Cajero
            $table->foreignId('cash_session_id')->nullable()->constrained()->onDelete('set null');
            $table->string('order_number', 30)->unique();
            $table->string('type', 20)->default('dine_in');
            $table->string('status', 20)->default('pending');
            $table->string('payment_status', 20)->default('pending');
            $table->integer('guests')->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->string('discount_type', 20)->nullable();
            $table->string('discount_reason')->nullable();
            $table->foreignId('discount_authorized_by')->nullable()->constrained('users');
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('tip_amount', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('kitchen_notes')->nullable();
            $table->string('source', 20)->default('pos'); // pos, web, app, rappi, ifood
            $table->string('external_id')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index(['table_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['order_number']);
            $table->index(['created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('quantity', 10, 3);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->boolean('is_courtesy')->default(false);
            $table->string('courtesy_reason')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });

        Schema::create('order_item_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('modifier_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('order_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('type', 20);
            $table->decimal('value', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->string('reason')->nullable();
            $table->string('code')->nullable();
            $table->foreignId('authorized_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('order_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('tax_type', 10);
            $table->string('tax_name', 50);
            $table->decimal('percentage', 5, 2);
            $table->decimal('base_amount', 12, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_taxes');
        Schema::dropIfExists('order_discounts');
        Schema::dropIfExists('order_item_modifiers');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
