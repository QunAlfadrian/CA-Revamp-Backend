<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('books', function (Blueprint $table) {
            $table->string('isbn', 13)->primary();
            $table->string('title', 255);
            $table->text('synopsis')->nullable();
            $table->string('author_1', 255);
            $table->string('author_2', 255)->nullable();
            $table->string('author_3', 255)->nullable();
            $table->string('published_year', 4)->nullable();
            $table->string('cover_image_url')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('books');
    }
};
