<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('requested_books', function (Blueprint $table) {
            $table->string('campaign_id', 15);
            $table->string('book_id', 13);
            $table->unsignedSmallInteger('requested_quantity')->default(0);
            $table->unsignedSmallInteger('donated_quantity')->default(0);
            $table->timestamps();

            // primary key constraint
            $table->primary(['campaign_id', 'book_id']);

            // foreign key constraints
            $table->foreign('book_id')->references('isbn')->on('books')->cascadeOnDelete();
            $table->foreign('campaign_id')->references('campaign_id')->on('campaigns');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('requested_books');
    }
};
