<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('xp')->default(0);
            $table->integer('level')->default(1);
            $table->json('choices_made')->nullable(); // Story choices
            $table->json('unlocked_pages')->nullable(); // Accessible story pages
            $table->integer('streak_days')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_states');
    }
};
