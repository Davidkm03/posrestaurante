<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku', 50)->nullable()->unique();
            $table->string('barcode', 50)->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('tax_type', 10)->default('01'); // IVA
            $table->decimal('tax_percentage', 5, 2)->default(19.00);
            $table->boolean('tax_included')->default(true);
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('track_inventory')->default(false);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(0);
            $table->string('unit')->default('und');
            $table->boolean('is_combo')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_pos')->default(true);
            $table->boolean('show_in_menu')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('availability_schedule')->nullable();
            $table->integer('prep_time_minutes')->nullable();
            $table->string('printer_destination')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'is_active']);
            $table->index(['sku']);
            $table->index(['barcode']);
        });

        // Tabla para variantes de producto
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('sku', 50)->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('cost', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
