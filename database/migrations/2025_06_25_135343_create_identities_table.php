<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('identities', function (Blueprint $table) {
            $table->string('identity_id', 17)->primary();
            $table->string('user_id', 14);
            $table->string('full_name', 127);
            $table->string('phone_number', 15)->nullable();
            $table->enum('gender', [
                'male',
                'female',
                'other'
            ])->default(null)->nullable();
            $table->text('profile_image_url')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nik', 16)->nullable();
            $table->text('id_card_image_url')->nullable();
            $table->timestamps();

            // foreign key constraints
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('identities');
    }
};
