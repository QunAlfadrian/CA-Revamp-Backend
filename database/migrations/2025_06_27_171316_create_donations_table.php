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
            $table->string('donor_id', 14)->nullable()->default(null);
            $table->string('donor_name', 50)->nullable()->default(null);
            $table->string('campaign_id', 15);
            $table->enum('type', [
                'fund',
                'item'
            ])->default('fund');
            $table->timestamp('verified_at');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('donor_id')->references('user_id')->on('users');
            $table->foreign('campaign_id')->references('campaign_id')->on('campaigns');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('donations');
    }
};
