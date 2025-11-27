<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Configuración de Factus por sucursal
        Schema::create('factus_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable(); // Encriptado
            $table->string('email')->nullable();
            $table->text('password')->nullable(); // Encriptado
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_sandbox')->default(true);
            $table->boolean('is_active')->default(false);
            $table->boolean('auto_send_invoice')->default(false);
            $table->boolean('auto_send_email')->default(false);
            $table->string('default_numbering_range_id')->nullable();
            $table->string('credit_note_range_id')->nullable();
            $table->json('company_info')->nullable();
            $table->json('subscription_info')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();

            $table->unique('branch_id');
        });

        // Logs de documentos electrónicos enviados
        Schema::create('factus_document_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->string('document_type', 30); // invoice, credit_note, support_document
            $table->string('document_number')->nullable();
            $table->string('uuid')->nullable()->index();
            $table->string('cufe')->nullable();
            $table->string('status', 30)->default('pending'); // pending, validated, rejected, error
            $table->json('request_payload')->nullable();
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('xml_url')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'document_type']);
            $table->index(['branch_id', 'status']);
        });

        // Catálogo de municipios (sincronizado desde Factus)
        Schema::create('factus_municipalities', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('name');
            $table->string('department_code', 10);
            $table->string('department_name');
            $table->timestamps();

            $table->index('department_code');
        });

        // Catálogo de tributos de productos
        Schema::create('factus_product_tributes', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('rate', 5, 2)->nullable();
            $table->timestamps();
        });

        // Catálogo de unidades de medida
        Schema::create('factus_unit_measures', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('name');
            $table->string('symbol', 10)->nullable();
            $table->timestamps();
        });

        // Rangos de numeración (sincronizado desde Factus)
        Schema::create('factus_numbering_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('factus_id')->index();
            $table->string('document_type', 30); // invoice, credit_note, debit_note, support_document
            $table->string('prefix', 10)->nullable();
            $table->bigInteger('from_number');
            $table->bigInteger('to_number');
            $table->bigInteger('current_number');
            $table->string('resolution_number')->nullable();
            $table->date('resolution_date')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->string('technical_key')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['branch_id', 'factus_id']);
        });

        // Agregar campos a la tabla de invoices para datos DIAN
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('dian_uuid')->nullable()->after('status');
            $table->string('dian_cufe')->nullable()->after('dian_uuid');
            $table->text('dian_qr')->nullable()->after('dian_cufe');
            $table->string('dian_status', 30)->nullable()->after('dian_qr');
            $table->json('dian_response')->nullable()->after('dian_status');
            $table->timestamp('dian_validated_at')->nullable()->after('dian_response');
            $table->boolean('dian_email_sent')->default(false)->after('dian_validated_at');
            
            $table->index('dian_uuid');
            $table->index('dian_status');
        });

        // Agregar campos a branches para configuración de Factus
        Schema::table('branches', function (Blueprint $table) {
            $table->string('factus_numbering_range_id')->nullable()->after('fiscal_responsibilities');
            $table->string('factus_credit_note_range_id')->nullable()->after('factus_numbering_range_id');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'factus_numbering_range_id',
                'factus_credit_note_range_id'
            ]);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'dian_uuid',
                'dian_cufe',
                'dian_qr',
                'dian_status',
                'dian_response',
                'dian_validated_at',
                'dian_email_sent'
            ]);
        });

        Schema::dropIfExists('factus_numbering_ranges');
        Schema::dropIfExists('factus_unit_measures');
        Schema::dropIfExists('factus_product_tributes');
        Schema::dropIfExists('factus_municipalities');
        Schema::dropIfExists('factus_document_logs');
        Schema::dropIfExists('factus_configurations');
    }
};
