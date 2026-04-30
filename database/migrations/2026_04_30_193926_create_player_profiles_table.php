<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

            // Temporal identity
            $table->string('temporal_id')->unique()->nullable();

            // Operator resonance
            $table->unsignedTinyInteger('age_at_contact')->nullable();
            $table->string('primary_affinity', 32)->nullable(); // mathematics, stories, building, discovery
            $table->string('secondary_affinity', 32)->nullable();

            // Vortex connection quality
            $table->decimal('vortex_stability', 3, 2)->default(0.50); // 0.0 - 1.0
            $table->string('current_phase', 32)->default('recruitment');
            $table->decimal('commander_trust', 3, 2)->default(0.00);
            $table->decimal('fear_level', 3, 2)->default(0.00);

            // Pattern analysis (what the AI has learned)
            $table->json('detected_struggles')->nullable();
            $table->json('detected_strengths')->nullable();

            // Progress
            $table->unsignedInteger('total_xp')->default(0);
            $table->string('faction_class', 32)->nullable();
            $table->json('completed_transmissions')->nullable();
            $table->json('unlocked_lore')->nullable();

            // Active state
            $table->foreignId('active_mission_id')->nullable();
            $table->foreignId('current_chat_session_id')->nullable();

            // Commander's notes (AI-generated summary)
            $table->text('commander_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_profiles');
    }
};
