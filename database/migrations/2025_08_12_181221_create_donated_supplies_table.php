<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('donated_supplies', function (Blueprint $table) {
            $table->string('donated_item_id');
            $table->string('requested_supply_id', 16);
            $table->unsignedSmallInteger('quantity')->default(0);
            $table->timestamps();

            // primary key constraint
            $table->primary(['donated_item_id', 'requested_supply_id']);

            // foreign key constraints
            $table
                ->foreign('donated_item_id')
                ->references('donated_item_id')
                ->on('donated_items');
            $table
                ->foreign('requested_supply_id')
                ->references('requested_supply_id')
                ->on('requested_supplies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('donated_supplies');
    }
};
