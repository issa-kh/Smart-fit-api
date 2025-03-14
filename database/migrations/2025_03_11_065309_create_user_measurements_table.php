<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_measurements', function (Blueprint $table) {
            $table->id();
            $table->decimal('height', 5, 2);
            $table->decimal('weight', 5, 2);
            $table->decimal('chest', 5, 2);
            $table->decimal('waist', 5, 2);
            $table->decimal('hips', 5, 2);
            $table->enum('gender',['male','female']);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_measurements');
    }
};
