<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('funds', function (Blueprint $table) {
            // $table->uuid('id')->primary();
            $table->string('id', 19)->primary(); // fund-31122025235959
            $table->foreignUuid('campaign_id')->constrained(
                'campaigns', 'id'
            )->cascadeOnDelete();
            $table->foreignUuid('donation_id')->constrained(
                'donations', 'id'
            );
            $table->decimal('amount', 20, 2)->default(5000);
            $table->string('status')->default('pending');
            $table->string('snap_token', 36)->nullable();
            $table->string('redirect_url', 75)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('funds');
    }
};
