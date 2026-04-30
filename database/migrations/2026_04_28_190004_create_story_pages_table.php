<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('story_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->json('content'); // Array of lines
            $table->json('choices')->nullable(); // Available choices
            $table->string('type')->default('story'); // menu, story, quiz, game
            $table->string('required_role')->nullable(); // Role required to access
            $table->timestamps();
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_pages');
    }
};
