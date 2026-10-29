<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_survey_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('pending'); // pending, found, missing
            $table->text('note')->nullable();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
            $table->unique(['inventory_survey_id', 'asset_id']);
            $table->index(['inventory_survey_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_survey_items');
    }
};
