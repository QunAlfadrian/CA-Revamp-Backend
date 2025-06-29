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
            $table->foreignUuid('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('book_id', 13);
            $table->unsignedSmallInteger('quantity')->default(0);
            $table->unsignedSmallInteger('donated_quantity')->default(0);

            $table->foreign('book_id')->references('isbn')->on('books')->cascadeOnDelete();
            $table->primary(['campaign_id', 'book_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('requested_books');
    }
};
