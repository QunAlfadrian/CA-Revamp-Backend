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
            $table->string('organizer_application_id', 14)->primary();
            $table->string('user_id', 14);
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected'
            ])->default('pending');
            $table->string('rejected_message')->default(null)->nullable();
            $table->string('reviewed_by', 14)->default(null)->nullable();
            $table->timestamp('reviewed_at')->default(null)->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('reviewed_by')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('organizer_applications');
    }
};
