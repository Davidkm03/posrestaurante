<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pin', 6)->nullable()->after('password');
            $table->string('document_type', 10)->nullable()->after('pin');
            $table->string('document_number', 20)->nullable()->after('document_type');
            $table->string('phone', 20)->nullable()->after('document_number');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('position')->nullable()->after('avatar');
            $table->decimal('commission_percentage', 5, 2)->default(0)->after('position');
            $table->boolean('is_active')->default(true)->after('commission_percentage');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'pin', 'document_type', 'document_number', 'phone',
                'avatar', 'position', 'commission_percentage',
                'is_active', 'last_login_at', 'deleted_at'
            ]);
        });
    }
};
