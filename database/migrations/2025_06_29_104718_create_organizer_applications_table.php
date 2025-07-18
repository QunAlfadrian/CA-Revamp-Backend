<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('organizer_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id');
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected'
            ])->default('pending');
            $table->string('rejected_message')->default(null)->nullable();
            $table->foreignUuid('reviewed_by')->default(null)->nullable();
            $table->timestamp('reviewed_at')->default(null)->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('reviewed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('organizer_applications');
    }
};
