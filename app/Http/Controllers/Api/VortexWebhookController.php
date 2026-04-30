<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArchiveEntry;
use App\Models\BlogPost;
use App\Models\PlayerProfile;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VortexWebhookController extends Controller
{
    /**
     * Shared secret auth for n8n webhooks.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $key = $request->header('X-Vortex-Key');
            $expected = config('services.vortex.webhook_key');

            if (empty($expected) || $key !== $expected) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $next($request);
        });
    }

    /**
     * Receive a generated blog post from n8n.
     *
     * POST /api/vortex/blog
     * Payload:
     * {
     *   "user_id": 1,
     *   "title": "Archive Chronicle: ...",
     *   "content": "markdown...",
     *   "narrative_beats": [...],
     *   "story_arc": "recruitment",
     *   "generated_by": "anthropic/claude-3.5-haiku"
     * }
     */
    public function receiveBlog(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'narrative_beats' => 'nullable|array',
            'story_arc' => 'nullable|string|max:64',
            'generated_by' => 'nullable|string|max:64',
        ]);

        // Store as a blog post (extend this with a BlogPost model later)
        $profile = PlayerProfile::where('user_id', $validated['user_id'])->first();

        $post = BlogPost::createFromN8n([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'narrative_beats' => $validated['narrative_beats'] ?? [],
            'story_arc' => $validated['story_arc'] ?? 'chronicle',
            'generated_by' => $validated['generated_by'] ?? null,
        ], $profile?->user);

        Log::info('Blog post received from n8n', [
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'blog_post_id' => $post->id,
            'slug' => $post->slug,
        ]);

        return response()->json([
            'status' => 'stored',
            'blog_post_id' => $post->id,
            'slug' => $post->slug,
            'temporal_id' => $profile?->temporal_id,
        ]);
    }

    /**
     * Receive a generated mission from n8n.
     *
     * POST /api/vortex/mission
     * Payload:
     * {
     *   "slug": "generated-mission-001",
     *   "title": "Mission: The Prime Breach",
     *   "content": ["line1", "line2", ...],
     *   "choices": [{"key": "1", "label": "...", "next_archive": "..."}],
     *   "conditions": {"min_age": 9, "max_age": 12, "affinity": "mathematics"},
     *   "narrative_beats": ["prime_numbers_introduced"],
     *   "generating_model": "anthropic/claude-3.5-haiku"
     * }
     */
    public function receiveMission(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:128|unique:archive_entries,slug',
            'title' => 'required|string|max:255',
            'content' => 'required|array',
            'content.*' => 'string',
            'choices' => 'nullable|array',
            'conditions' => 'nullable|array',
            'narrative_beats' => 'nullable|array',
            'generating_model' => 'nullable|string|max:64',
        ]);

        $entry = ArchiveEntry::create([
            'slug' => $validated['slug'],
            'type' => 'mission',
            'title' => $validated['title'],
            'content' => $validated['content'],
            'choices' => $validated['choices'] ?? null,
            'conditions' => $validated['conditions'] ?? null,
            'vortex_state_required' => 'any',
            'narrative_beats' => $validated['narrative_beats'] ?? null,
            'is_generated' => true,
            'generating_model' => $validated['generating_model'] ?? null,
        ]);

        Log::info('Mission received from n8n and stored in Archive', [
            'slug' => $entry->slug,
            'model' => $entry->generating_model,
        ]);

        return response()->json([
            'status' => 'stored',
            'archive_id' => $entry->id,
            'slug' => $entry->slug,
        ]);
    }

    /**
     * Receive a graded report from n8n.
     *
     * POST /api/vortex/report
     * Payload:
     * {
     *   "report_id": 1,
     *   "assessment": "Excellent pattern recognition...",
     *   "xp_awarded": 50,
     *   "commander_response": "ZETA-7: Outstanding work, operator...",
     *   "detected_struggles": ["fractions"],
     *   "detected_strengths": ["prime_numbers"]
     * }
     */
    public function receiveReportGrade(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'report_id' => 'required|integer|exists:reports,id',
            'assessment' => 'required|string',
            'xp_awarded' => 'required|integer|min:0|max:100',
            'commander_response' => 'required|string',
            'detected_struggles' => 'nullable|array',
            'detected_strengths' => 'nullable|array',
        ]);

        $report = Report::findOrFail($validated['report_id']);
        $report->markGraded(
            assessment: $validated['assessment'],
            xp: $validated['xp_awarded'],
            commanderResponse: $validated['commander_response'],
        );

        // Update player profile with new insights
        $profile = PlayerProfile::where('user_id', $report->user_id)->first();
        if ($profile) {
            $struggles = $profile->detected_struggles ?? [];
            $strengths = $profile->detected_strengths ?? [];

            foreach ($validated['detected_struggles'] ?? [] as $s) {
                if (!in_array($s, $struggles, true)) {
                    $struggles[] = $s;
                }
            }

            foreach ($validated['detected_strengths'] ?? [] as $s) {
                if (!in_array($s, $strengths, true)) {
                    $strengths[] = $s;
                }
            }

            $profile->detected_struggles = $struggles;
            $profile->detected_strengths = $strengths;
            $profile->addXp($validated['xp_awarded']);
            $profile->save();
        }

        Log::info('Report graded by n8n', [
            'report_id' => $report->id,
            'xp' => $validated['xp_awarded'],
        ]);

        return response()->json([
            'status' => 'graded',
            'report_id' => $report->id,
            'xp_awarded' => $validated['xp_awarded'],
        ]);
    }

    /**
     * Get queued reports for n8n to process.
     *
     * GET /api/vortex/reports/queued
     */
    public function getQueuedReports(): JsonResponse
    {
        $reports = Report::pending()
            ->with(['user', 'chatSession'])
            ->limit(10)
            ->get()
            ->map(fn (Report $r) => [
                'report_id' => $r->id,
                'user_id' => $r->user_id,
                'mission_slug' => $r->mission_slug,
                'answers' => $r->answers,
                'submitted_at' => $r->submitted_at->toIso8601String(),
                'profile' => PlayerProfile::where('user_id', $r->user_id)->first()?->toArray(),
            ]);

        return response()->json([
            'count' => $reports->count(),
            'reports' => $reports,
        ]);
    }

    /**
     * Get narrative arcs ready for blog generation.
     *
     * GET /api/vortex/arcs/completed
     */
    public function getCompletedArcs(): JsonResponse
    {
        $profiles = PlayerProfile::whereNotNull('completed_transmissions')
            ->whereJsonLength('completed_transmissions', '>=', 3)
            ->with('user')
            ->limit(10)
            ->get()
            ->map(fn (PlayerProfile $p) => [
                'user_id' => $p->user_id,
                'temporal_id' => $p->temporal_id,
                'age' => $p->age_at_contact,
                'affinity' => $p->primary_affinity,
                'completed_transmissions' => $p->completed_transmissions,
                'narrative_beats' => $p->chatSessions()
                    ->whereNotNull('narrative_beats')
                    ->pluck('narrative_beats')
                    ->flatten()
                    ->values(),
                'commander_notes' => $p->commander_notes,
            ]);

        return response()->json([
            'count' => $profiles->count(),
            'arcs' => $profiles,
        ]);
    }
}
