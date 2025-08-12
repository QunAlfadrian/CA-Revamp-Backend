<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained(
                'users', 'user_id'
            )->cascadeOnDelete();
            $table->foreignUuid('role_id')->constrained(
                'roles', 'role_id'
            )->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('role_user');
    }
};
