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
            
            $table->foreignUuid('campaign_id')->constrained(
                'campaigns', 'id'
            )->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('campaign_images');
    }
};
