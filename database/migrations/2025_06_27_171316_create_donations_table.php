<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('donations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('donor_id')->constrained(
                'users', 'id'
            )->cascadeOnDelete();
            $table->foreignUuid('campaign_id')->constrained(
                'campaigns', 'id'
            )->cascadeOnDelete();
            $table->enum('type', [
                'fund',
                'item'
            ])->default('fund');
            $table->timestamp('verified_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('donations');
    }
};
