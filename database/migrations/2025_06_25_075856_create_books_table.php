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
            $table->string('title', 255)->unique();
            $table->string('slug', 255);
            $table->text('synopsis')->nullable()->default(null);
            $table->string('author_1', 255);
            $table->string('author_2', 255)->nullable()->default(null);
            $table->string('author_3', 255)->nullable()->default(null);
            $table->string('published_year', 4);
            $table->text('cover_image_url')->nullable()->default(null);
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
