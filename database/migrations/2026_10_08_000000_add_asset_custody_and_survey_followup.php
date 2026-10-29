<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('building')->nullable();
            $table->string('room')->nullable();
            $table->foreignId('custodian_user_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::table('inventory_survey_items', function (Blueprint $table) {
            $table->string('resolution_status')->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
        });

        Schema::create('asset_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
            $table->index(['asset_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_audit_logs');
        Schema::table('inventory_survey_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropColumn(['resolution_status', 'resolution_note', 'resolved_at']);
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('custodian_user_id');
            $table->dropColumn(['department', 'building', 'room']);
        });
    }
};
