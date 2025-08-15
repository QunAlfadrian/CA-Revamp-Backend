<?php

use App\FundStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('funds', function (Blueprint $table) {
            // $table->uuid('id')->primary();
            $table->uuid('id')->primary();
            $table->string('order_id', 25);
            $table->string('campaign_id', 15);
            $table->string('donation_id', 19);
            $table->decimal('amount', 10, 0)->default(5000);
            $table->decimal('service_fee', 4, 0)->default(0);
            $table->enum('status', array_map(fn($status) => $status->value, FundStatus::cases()))
                ->default(FundStatus::Pending->value);
            $table->string('snap_token', 50)->nullable();
            $table->string('redirect_url', 100)->nullable();
            $table->timestamps();

            // foreign key constraints
            $table->foreign('campaign_id')->references('campaign_id')->on('campaigns');
            $table->foreign('donation_id')->references('donation_id')->on('donations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('funds');
    }
};
