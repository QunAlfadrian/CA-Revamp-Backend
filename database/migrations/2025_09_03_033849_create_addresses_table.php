<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->string('campaign_id', 13)->primary();
            $table->string('address_detail', 255);
            $table->string('village', 50);
            $table->string('sub_district', 50);
            $table->string('city', 50);
            $table->string('province', 50);
            $table->string('postal_code', 5);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('campaign_id')->references('campaign_id')->on('campaigns')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('addresses');
    }
};
