<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organizer_id')->constrained(
                'users', 'id'
            )->cascadeOnDelete();
            $table->enum('type', [
                'fundraiser',
                'product_donation'
            ])->default('fundraiser');
            $table->string('title', 255)->unique();
            $table->string('slug', 50)->unique();
            $table->text('description');
            $table->text('header_image_url')->nullable()->default(null);
            $table->enum('status', [
                'pending',
                'on_progress',
                'finished',
                'rejected'
            ])->default('pending');
            $table->decimal('requested_fund_amount', 10, 0)->default(0);
            $table->decimal('donated_fund_amount', 10, 0)->default(0);
            $table->unsignedSmallInteger('requested_item_quantity')->default(0);
            $table->unsignedSmallInteger('donated_item_quantity')->default(0);
            $table->uuid('reviewed_by')->nullable()->default(null);
            $table->timestamp('reviewed_at')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('reviewed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('campaigns');
    }
};
