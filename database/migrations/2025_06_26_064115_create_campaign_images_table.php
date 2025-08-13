<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('campaign_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('slug');
            $table->string('alternative_text', 255);
            $table->string('filename')->nullable();
            $table->text('url');
            $table->string('campaign_id', 15);
            $table->timestamps();

            // foreign key constraints
            $table->foreign('campaign_id')->references('campaign_id')->on('campaigns')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('campaign_images');
    }
};
