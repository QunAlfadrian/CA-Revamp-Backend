<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('facilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 25);
            $table->string('description', 255);
            $table->unsignedSmallInteger('requested_quantity')->default(0);
            $table->unsignedSmallInteger('donated_quantity')->default(0);
            $table->string('campaign_id', 15);
            $table->timestamps();

            // foreign key constraints
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('facilities');
    }
};
