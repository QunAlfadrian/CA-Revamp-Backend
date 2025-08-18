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
            $table->string('campaign_id', 13)->primary();
            $table->string('organizer_id', 14);
            $table->enum('type', [
                'fundraiser',
                'product_donation'
            ])->default('fundraiser');
            $table->string('title', 255)->unique();
            $table->string('slug', 15)->unique();
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
            $table->decimal('withdrawn_fund', 10, 0)->default(0);
            $table->unsignedSmallInteger('requested_item_quantity')->default(0);
            $table->unsignedSmallInteger('donated_item_quantity')->default(0);
            $table->string('reviewed_by', 14)->nullable()->default(null);
            $table->timestamp('reviewed_at')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('organizer_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('reviewed_by')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('campaigns');
    }
};
