<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status', 16)->default('active'); // active, archived, completed
            $table->string('vortex_state_at_start', 16)->nullable(); // stable, unstable, down
            $table->foreignId('current_archive_entry_id')->nullable();
            $table->json('choices_made')->nullable(); // [{archive_slug, choice_key, timestamp}]
            $table->json('narrative_beats')->nullable(); // accumulated for blog generation
            $table->json('context_summary')->nullable(); // compressed for LLM window management
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
