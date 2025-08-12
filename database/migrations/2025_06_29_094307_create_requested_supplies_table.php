<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('requested_supplies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->text('description', 1024);
            $table->decimal('price', 10, 0)->default(0)->nullable();
            $table->unsignedSmallInteger('requested_quantity')->default(0);
            $table->unsignedSmallInteger('donated_quantity')->default(0);
            $table->enum('status', [
                'pending_verification',
                'on_delivery',
                'received',
                'not_received',
                'cancelled'
            ])->default('pending_verification');
            $table->foreignUuid('campaign_id')->nullable();
            $table->string('reviewed_by', 14)->nullable()->default(null);
            $table->timestamp('reviewed_at')->nullable()->default(null);
            $table->timestamps();

            // foreign key constraints
            $table->foreign('reviewed_by')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('requested_supplies');
    }
};
