<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name', 128)->default('ZETA-7');
            $table->string('author_avatar', 255)->nullable();
            $table->string('category', 64)->default('chronicle'); // chronicle, mission, lore, update
            $table->json('tags')->nullable();
            $table->json('narrative_beats')->nullable();
            $table->string('story_arc', 64)->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->string('generating_model', 64)->nullable();
            $table->string('status', 16)->default('draft'); // draft, published, archived
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['category', 'status']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
