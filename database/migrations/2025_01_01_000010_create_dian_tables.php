<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dian_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('resolution_number', 50);
            $table->date('resolution_date');
            $table->string('prefix', 10)->nullable();
            $table->bigInteger('range_from');
            $table->bigInteger('range_to');
            $table->bigInteger('current_number');
            $table->string('technical_key')->nullable();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->string('environment', 20)->default('test'); // test, production
            $table->string('document_type', 10)->default('01'); // 01=invoice, 91=credit_note, 92=debit_note
            $table->boolean('is_active')->default(true);
            $table->boolean('is_contingency')->default(false);
            $table->timestamps();

            $table->unique(['branch_id', 'prefix', 'document_type']);
            $table->index(['is_active', 'valid_to']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('restrict');
            $table->foreignId('order_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('resolution_id')->constrained('dian_resolutions')->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('invoice_number', 30);
            $table->string('prefix', 10)->nullable();
            $table->string('invoice_type', 20)->default('invoice'); // invoice, pos, contingency
            $table->date('issue_date');
            $table->time('issue_time');
            $table->date('due_date')->nullable();
            $table->string('payment_form', 10)->default('1'); // 1=contado, 2=credito
            $table->string('payment_method_code', 10)->default('10');
            $table->string('currency_code', 3)->default('COP');
            $table->decimal('exchange_rate', 12, 6)->default(1);
            $table->decimal('subtotal', 14, 2);
            $table->decimal('total_discount', 14, 2)->default(0);
            $table->decimal('total_tax_iva', 14, 2)->default(0);
            $table->decimal('total_tax_inc', 14, 2)->default(0);
            $table->decimal('total_tax_other', 14, 2)->default(0);
            $table->decimal('total_withholdings', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->text('notes')->nullable();
            $table->string('cufe', 100)->nullable();
            $table->text('qr_data')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('dian_response')->nullable();
            $table->string('dian_track_id')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['prefix', 'invoice_number']);
            $table->index(['branch_id', 'status']);
            $table->index(['cufe']);
            $table->index(['issue_date']);
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('line_number');
            $table->string('code', 50)->nullable();
            $table->string('description');
            $table->string('unit_code', 10)->default('94'); // DIAN unit code
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_price', 14, 2);
            $table->decimal('discount', 14, 2)->default(0);
            $table->string('tax_type', 10)->default('01');
            $table->decimal('tax_percentage', 5, 2)->default(19);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2);
            $table->timestamps();
        });

        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('restrict');
            $table->foreignId('invoice_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('resolution_id')->constrained('dian_resolutions')->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('note_number', 30);
            $table->string('prefix', 10)->nullable();
            $table->date('issue_date');
            $table->time('issue_time');
            $table->string('correction_concept', 10); // 1=devolución, 2=anulación, 3=descuento, 4=otros
            $table->text('reason');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('total_discount', 14, 2)->default(0);
            $table->decimal('total_tax', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->string('cude', 100)->nullable();
            $table->text('qr_data')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('dian_response')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['prefix', 'note_number']);
        });

        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('restrict');
            $table->foreignId('invoice_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('resolution_id')->constrained('dian_resolutions')->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('note_number', 30);
            $table->string('prefix', 10)->nullable();
            $table->date('issue_date');
            $table->time('issue_time');
            $table->string('correction_concept', 10);
            $table->text('reason');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('total_tax', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->string('cude', 100)->nullable();
            $table->text('qr_data')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('dian_response')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['prefix', 'note_number']);
        });

        Schema::create('dian_documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('document_type', 10);
            $table->string('cufe_cude', 100)->nullable();
            $table->longText('xml_content')->nullable();
            $table->longText('signed_xml')->nullable();
            $table->longText('dian_response_xml')->nullable();
            $table->string('status', 20)->default('pending');
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('track_id')->nullable();
            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dian_documents');
        Schema::dropIfExists('debit_notes');
        Schema::dropIfExists('credit_notes');
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('dian_resolutions');
    }
};
