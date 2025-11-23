<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reservaciones
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('table_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->string('customer_email')->nullable();
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->integer('party_size');
            $table->integer('duration_minutes')->default(90);
            $table->string('status', 20)->default('pending');
            $table->text('special_requests')->nullable();
            $table->text('internal_notes')->nullable();
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->boolean('deposit_paid')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'reservation_date']);
            $table->index(['status']);
        });

        // Promociones
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code', 30)->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('type', 20); // percentage, fixed, bogo, combo
            $table->decimal('value', 12, 2);
            $table->decimal('min_purchase', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->json('days_of_week')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->integer('per_customer_limit')->nullable();
            $table->boolean('combinable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->string('rule_type', 30); // category, product, payment_method
            $table->string('rule_operator', 20); // include, exclude
            $table->json('rule_values');
            $table->timestamps();
        });

        // Impresoras
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type', 20)->default('receipt');
            $table->string('connection_type', 20)->default('network'); // network, usb, bluetooth
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->string('device_path')->nullable();
            $table->integer('paper_width')->default(80);
            $table->boolean('auto_cut')->default(true);
            $table->boolean('open_drawer')->default(false);
            $table->json('categories')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type', 30); // receipt, kitchen_ticket, invoice
            $table->longText('content');
            $table->string('status', 20)->default('pending');
            $table->integer('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();
        });

        // Configuraciones
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('group', 50)->default('general');
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->timestamps();

            $table->unique(['branch_id', 'group', 'key']);
        });

        // Impuestos configurables
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('dian_code', 10);
            $table->decimal('percentage', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Auditoría
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action', 50);
            $table->string('module', 50);
            $table->nullableMorphs('auditable');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action']);
            $table->index(['created_at']);
        });

        // Puntos de fidelidad
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type', 20); // earned, redeemed, expired, adjustment
            $table->integer('points');
            $table->integer('balance_after');
            $table->string('description')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        // Combos
        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->boolean('is_optional')->default(false);
            $table->decimal('price_adjustment', 12, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_items');
        Schema::dropIfExists('combos');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('print_jobs');
        Schema::dropIfExists('printers');
        Schema::dropIfExists('promotion_rules');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('reservations');
    }
};
