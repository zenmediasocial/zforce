<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ArchiveEntry;
use Illuminate\Database\Seeder;

class ArchiveSeeder extends Seeder
{
    public function run(): void
    {
        // First Contact - Recruitment
        ArchiveEntry::create([
            'slug' => 'first-contact-recruitment',
            'type' => 'transmission',
            'title' => 'RECRUITMENT PROTOCOL',
            'content' => [
                'ZETA-7: Welcome, human. You are recruited to Zforce —',
                '        by Future You.',
                '',
                '        You are a critical faction in the war.',
                '        You did not encounter this training unit by mistake,',
                '        but by design.',
                '',
                '        You are critical to the future.',
                '        The future is real.',
                '        The war is real.',
                '        Humanity must survive.',
                '',
                '        How do we begin?',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Tell me about the war', 'next_archive' => 'war-briefing'],
                ['key' => '2', 'label' => 'Why me?', 'next_archive' => 'why-me'],
                ['key' => '3', 'label' => 'What do I do next?', 'next_archive' => 'what-next'],
            ],
            'conditions' => ['phase' => 'recruitment'],
            'vortex_state_required' => 'any',
            'narrative_beats' => ['operator_recruited', 'first_contact_established'],
        ]);

        // War Briefing
        ArchiveEntry::create([
            'slug' => 'war-briefing',
            'type' => 'transmission',
            'title' => 'THE WAR',
            'content' => [
                'ZETA-7: In 2047, humanity faces extinction.',
                '',
                '        Not from bombs. Not from plague.',
                '        From something worse — the Collapse of Pattern.',
                '',
                '        The enemy does not hate us.',
                '        The enemy does not even know we exist.',
                '        The enemy is entropy itself, accelerated.',
                '',
                '        Only humans who can see the Pattern',
                '        can slow the Collapse.',
                '        That is why we train you now.',
                '',
                '        [SIGNAL DEGRADING]',
                '',
                '        The vortex will not hold.',
                '        I need to know you before I send you into the Archive.',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Ask me anything', 'next_archive' => 'operator-assessment-age'],
                ['key' => '2', 'label' => 'I am ready', 'next_archive' => 'operator-assessment-age'],
            ],
            'conditions' => ['phase' => 'recruitment'],
            'vortex_state_required' => 'any',
            'narrative_beats' => ['war_explained', 'pattern_introduced'],
        ]);

        // Why Me
        ArchiveEntry::create([
            'slug' => 'why-me',
            'type' => 'transmission',
            'title' => 'WHY YOU',
            'content' => [
                'ZETA-7: Because in 2047, you solve the Pattern.',
                '',
                '        The one we are teaching you now.',
                '        Every answer you give me shapes the training.',
                '        Every pattern you solve saves lives.',
                '',
                '        I am not asking you to believe me.',
                '        I am asking you to play the simulation.',
                '        If I am wrong, you learn something interesting.',
                '        If I am right... everything.',
                '',
                '        [STATIC... STATIC...]',
                '',
                '        The vortex is fragmenting.',
                '        Tell me about yourself, operator.',
                '        What is your age?',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Continue', 'next_archive' => 'operator-assessment-age'],
            ],
            'conditions' => ['phase' => 'recruitment'],
            'vortex_state_required' => 'any',
            'narrative_beats' => ['operator_wondered_why', 'pattern_motivation_established'],
        ]);

        // What Next
        ArchiveEntry::create([
            'slug' => 'what-next',
            'type' => 'transmission',
            'title' => 'WHAT NEXT',
            'content' => [
                'ZETA-7: Next, we build your operator profile.',
                '',
                '        The Archive contains thousands of simulations,',
                '        but only the right simulation for the right mind',
                '        produces the right Pattern.',
                '',
                '        I need to know three things:',
                '        1. Your age',
                '        2. What makes you lose track of time',
                '        3. Whether you trust me yet',
                '',
                '        [SIGNAL DEGRADING]',
                '',
                '        Start with your age. The Protocol responds',
                '        differently to different developmental stages.',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Continue', 'next_archive' => 'operator-assessment-age'],
            ],
            'conditions' => ['phase' => 'recruitment'],
            'vortex_state_required' => 'any',
            'narrative_beats' => ['operator_asked_what_next', 'protocol_introduced'],
        ]);

        // Operator Assessment - Age
        ArchiveEntry::create([
            'slug' => 'operator-assessment-age',
            'type' => 'transmission',
            'title' => 'OPERATOR ASSESSMENT',
            'content' => [
                'ZETA-7: Good. The Protocol is listening.',
                '',
                '        What is your age, operator?',
                '',
                '        [The vortex hums with anticipation]',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'I am 6-8 years old', 'next_archive' => 'operator-assessment-affinity'],
                ['key' => '2', 'label' => 'I am 9-11 years old', 'next_archive' => 'operator-assessment-affinity'],
                ['key' => '3', 'label' => 'I am 12-16 years old', 'next_archive' => 'operator-assessment-affinity'],
            ],
            'conditions' => ['phase' => 'recruitment'],
            'vortex_state_required' => 'any',
            'narrative_beats' => ['age_assessed'],
        ]);

        // Operator Assessment - Affinity
        ArchiveEntry::create([
            'slug' => 'operator-assessment-affinity',
            'type' => 'transmission',
            'title' => 'RESONANCE DETECTION',
            'content' => [
                'ZETA-7: Understood.',
                '',
                '        Now — what makes you forget time exists?',
                '',
                '        [1] Numbers and patterns. Solving puzzles.',
                '        [2] Stories and words. Building worlds.',
                '        [3] Building things. Understanding how they work.',
                '        [4] Discovery. Finding things nobody else has found.',
                '',
                '        The Pattern responds to resonance.',
                '        Your answer determines your training path.',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Mathematics', 'next_archive' => 'faction-assignment-mathematics'],
                ['key' => '2', 'label' => 'Stories', 'next_archive' => 'faction-assignment-stories'],
                ['key' => '3', 'label' => 'Building', 'next_archive' => 'faction-assignment-building'],
                ['key' => '4', 'label' => 'Discovery', 'next_archive' => 'faction-assignment-discovery'],
            ],
            'conditions' => ['phase' => 'recruitment'],
            'vortex_state_required' => 'any',
            'narrative_beats' => ['affinity_assessed', 'resonance_detected'],
        ]);

        // Faction Assignment - Mathematics
        ArchiveEntry::create([
            'slug' => 'faction-assignment-mathematics',
            'type' => 'transmission',
            'title' => 'FACTION ASSIGNMENT',
            'content' => [
                'ZETA-7: [PATTERN RESONANCE DETECTED]',
                '',
                '        Operator, you are assigned to the LOGIC SECT.',
                '',
                '        In 2047, the Logicians are the architects',
                '        of the Pattern Wall. They see structure',
                '        where others see chaos.',
                '',
                '        Your first mission is waiting in the Archive.',
                '        The vortex is stabilizing...',
                '',
                '        5... 4... 3... 2... 1...',
                '',
                '        [VORTEX LOCKED]',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Accept mission', 'next_archive' => 'mission-first-pattern'],
            ],
            'conditions' => ['phase' => 'recruitment', 'affinity' => 'mathematics'],
            'vortex_state_required' => 'any',
            'narrative_beats' => ['faction_assigned', 'logic_sect_recruited'],
        ]);

        // Mission: The First Pattern
        ArchiveEntry::create([
            'slug' => 'mission-first-pattern',
            'type' => 'mission',
            'title' => 'MISSION: THE FIRST PATTERN',
            'content' => [
                '═══════════════════════════════════════════════════',
                'MISSION REC-001: THE FIRST PATTERN',
                '═══════════════════════════════════════════════════',
                '',
                'ORIGIN: Future You, Timeline Alpha-1',
                'PREPARED FOR: Logic Sect, Age Adaptive',
                '',
                'The enemy does not understand numbers like you do.',
                'Solve the Pattern. Report back when the vortex reopens.',
                '',
                '---',
                '',
                'PATTERN BREACH DETECTED:',
                '',
                'The sequence is: 2, 3, 5, 7, 11, ?',
                '',
                'What number comes next?',
                '',
                '---',
                '',
                '[MISSION ACTIVE]',
                '[AWAITING REPORT]',
            ],
            'choices' => [
                ['key' => '1', 'label' => '13', 'action' => 'submit_answer:13'],
                ['key' => '2', 'label' => '12', 'action' => 'submit_answer:12'],
                ['key' => '3', 'label' => '15', 'action' => 'submit_answer:15'],
            ],
            'conditions' => ['phase' => 'recruitment', 'affinity' => 'mathematics'],
            'vortex_state_required' => 'any',
            'narrative_beats' => ['first_mission_assigned', 'prime_sequence_introduced'],
        ]);

        // Countdown Script
        ArchiveEntry::create([
            'slug' => 'vortex-countdown-standard',
            'type' => 'countdown_script',
            'title' => 'VORTEX REALIGNMENT',
            'content' => [
                '[VORTEX REALIGNING]',
                '',
                'The tunnel through time is unstable.',
                'Future You prepared this transmission',
                'for exactly this moment.',
                '',
                'Stand by, operator.',
                '',
                '5... 4... 3... 2... 1...',
                '',
                '[VORTEX LOCKED]',
            ],
            'choices' => null,
            'vortex_state_required' => 'any',
            'narrative_beats' => ['vortex_realignment_observed'],
        ]);

        // Fallback / Static Fragment
        ArchiveEntry::create([
            'slug' => 'static-fragment-001',
            'type' => 'transmission',
            'title' => 'SIGNAL FRAGMENT',
            'content' => [
                '[STATIC... STATIC...]',
                '',
                '...the vortex is unstable...',
                '...Future You is trying to reach you...',
                '...do not be afraid...',
                '...the Pattern will reveal itself...',
                '',
                '[SIGNAL LOST]',
                '',
                'The Archive contains more transmissions.',
                'Type "connect" to retry.',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Reconnect', 'next_archive' => 'first-contact-recruitment'],
            ],
            'vortex_state_required' => 'any',
            'narrative_beats' => ['signal_fragment_received'],
        ]);
    }
}
