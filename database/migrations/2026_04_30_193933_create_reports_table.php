<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('chat_session_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('archive_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->string('mission_slug', 64)->nullable();
            $table->json('answers'); // player's answers/choices
            $table->json('assessment')->nullable(); // AI grading result
            $table->unsignedInteger('xp_awarded')->default(0);
            $table->string('status', 16)->default('queued'); // queued, processing, graded, archived
            $table->timestamp('submitted_at');
            $table->timestamp('processed_at')->nullable();
            $table->text('commander_response')->nullable(); // ZETA-7's response after grading
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('mission_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
