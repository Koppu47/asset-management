<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prevent deleting records that still have related history.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
        });

        Schema::table('borrow_records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['asset_id']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('asset_id')->references('id')->on('assets')->restrictOnDelete();
        });
    }

    /**
     * Restore the original cascading delete behavior.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });

        Schema::table('borrow_records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['asset_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
        });
    }
};
