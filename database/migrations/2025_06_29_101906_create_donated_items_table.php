<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('donated_items', function (Blueprint $table) {
            $table->string('donated_item_id', 25)->primary();
            $table->string('campaign_id', 15);
            $table->string('donation_id', 19);
            $table->unsignedSmallInteger('quantity')->default(0);
            $table->text('package_picture_url')->nullable();
            $table->enum('delivery_service', [
                'pos_indonesia',
                'jne',
                'jnt',
                'sicepat',
                'anteraja',
                'lion_parcel',
                'spx_express',
                'dhl'
            ])->default('pos_indonesia');
            $table->string('resi', 25);
            $table->enum('status', [
                'pending_verification',
                'on_delivery',
                'received',
                'not_received',
                'cancelled',
                'declined'
            ])->default('pending_verification');
            $table->string('reviewed_by', 14)->nullable()->default(null);
            $table->timestamp('reviewed_at')->nullable()->default(null);
            $table->timestamps();

            // foreign key constraints
            $table->foreign('campaign_id')->references('campaign_id')->on('campaigns');
            $table->foreign('donation_id')->references('donation_id')->on('donations');
            $table->foreign('reviewed_by')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('donated_items');
    }
};
