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
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id')->constrained(
                'campaigns', 'id'
            );
            $table->foreignUuid('donation_id')->constrained(
                'donations', 'id'
            );
            $table->unsignedSmallInteger('donated_item_quantity')->default(0);
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
            $table->string('resi');
            $table->enum('status', [
                'pending_verification',
                'on_delivery',
                'received',
                'not_received',
                'cancelled',
                'declined'
            ])->default('pending_verification');
            $table->uuid('reviewed_by')->nullable()->default(null);
            $table->timestamp('reviewed_at')->nullable()->default(null);
            $table->foreign('reviewed_by')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('donated_items');
    }
};
