<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->onDelete('cascade');
            $table->string('role', 16); // system, commander, user, archive
            $table->text('content');
            $table->json('choices')->nullable(); // if commander/archive offers choices
            $table->string('selected_choice', 16)->nullable();
            $table->json('narrative_beats')->nullable(); // extractable story moments
            $table->string('source', 16)->nullable(); // realtime, archive, queued
            $table->string('model_used', 64)->nullable(); // which LLM generated this
            $table->json('metadata')->nullable(); // tokens, latency, cost, etc.
            $table->unsignedInteger('sequence')->default(0); // ordering within session
            $table->timestamps();

            $table->index(['chat_session_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
