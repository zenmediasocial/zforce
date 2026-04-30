<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archive_entries', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type', 32); // transmission, mission, lore_fragment, countdown_script
            $table->string('title')->nullable();
            $table->json('content'); // array of lines
            $table->json('choices')->nullable(); // [{key, label, next_archive, action}]
            $table->json('conditions')->nullable(); // {min_age, max_age, affinity, phase, vortex_state}
            $table->string('vortex_state_required', 16)->default('any'); // stable, unstable, down, any
            $table->json('narrative_beats')->nullable(); // extractable for blog generation
            $table->json('used_by')->nullable(); // array of user_ids
            $table->boolean('is_generated')->default(false); // false = hand-crafted, true = AI-generated
            $table->string('generating_model', 64)->nullable(); // which model generated this
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'vortex_state_required']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archive_entries');
    }
};
