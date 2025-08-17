<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('donated_books', function (Blueprint $table) {
            $table->string('donated_item_id');
            $table->string('book_id', 13);
            $table->unsignedSmallInteger('quantity')->default(0);
            $table->timestamps();

            // primary key constraint
            $table->primary(['donated_item_id', 'book_id']);

            // foreign key constraints
            $table->foreign('donated_item_id')->references('id')->on('donated_items');
            $table->foreign('book_id')->references('isbn')->on('books');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('donated_books');
    }
};
