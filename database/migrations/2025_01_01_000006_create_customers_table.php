<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_type', 20)->default('natural');
            $table->string('document_type', 10)->default('13'); // CC
            $table->string('document_number', 20);
            $table->string('verification_digit', 1)->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('department')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('tax_regime', 30)->nullable();
            $table->json('fiscal_responsibilities')->nullable();
            $table->string('economic_activity', 10)->nullable();
            $table->text('notes')->nullable();
            $table->text('allergies')->nullable();
            $table->text('preferences')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->string('loyalty_level', 20)->default('bronze');
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->decimal('credit_balance', 12, 2)->default(0);
            $table->date('birthdate')->nullable();
            $table->boolean('accepts_marketing')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['document_type', 'document_number']);
            $table->index(['email']);
            $table->index(['phone']);
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('label')->default('Principal');
            $table->string('address');
            $table->string('address_detail')->nullable();
            $table->string('city');
            $table->string('department')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
    }
};
