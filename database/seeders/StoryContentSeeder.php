<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\StoryPage;
use Illuminate\Database\Seeder;

class StoryContentSeeder extends Seeder
{
    public function run(): void
    {
        StoryPage::create([
            'slug' => 'welcome',
            'title' => 'welcome.txt',
            'content' => [
                'WELCOME TO THE TERMINAL LEARNING SYSTEM',
                '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
                '',
                'You have accessed a secure educational environment',
                'designed for young minds aged 6-16.',
                '',
                'Your mission: Explore, learn, and grow.',
                'Every challenge completed earns you experience.',
                'Every question answered unlocks new knowledge.',
                '',
                'Type "help" for available commands.',
                'Type "play" to begin your learning journey.',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Play', 'target' => 'menu-play'],
                ['key' => '2', 'label' => 'Stats', 'target' => 'menu-stats'],
                ['key' => '3', 'label' => 'Help', 'target' => 'menu-help'],
            ],
            'type' => 'menu',
        ]);

        StoryPage::create([
            'slug' => 'menu-play',
            'title' => 'play.exe',
            'content' => [
                'LEARNING MODULES',
                '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
                '',
                'Select a module to begin:',
                '',
                ' [1] Mathematics - Numbers & Patterns',
                ' [2] Science - Discovery & Experiment',
                ' [3] Language - Words & Stories',
                ' [4] History - Time & Civilization',
                ' [5] Art - Creativity & Expression',
                '',
                'Type the number to select a module.',
                'Type "back" to return to main menu.',
            ],
            'choices' => [
                ['key' => '1', 'label' => 'Mathematics', 'target' => 'math-start'],
                ['key' => '2', 'label' => 'Science', 'target' => 'science-start'],
                ['key' => '3', 'label' => 'Language', 'target' => 'language-start'],
                ['key' => '4', 'label' => 'History', 'target' => 'history-start'],
                ['key' => '5', 'label' => 'Art', 'target' => 'art-start'],
            ],
            'type' => 'menu',
        ]);

        StoryPage::create([
            'slug' => 'menu-help',
            'title' => 'help.txt',
            'content' => [
                'AVAILABLE COMMANDS',
                '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
                '',
                ' NAVIGATION:',
                '  [1-9]     Select menu option',
                '  back      Return to previous menu',
                '  home      Return to main menu',
                '',
                ' COMMANDS:',
                '  help      Show this help message',
                '  clear     Clear terminal screen',
                '  play      Enter learning mode',
                '  stats     View your progress',
                '  logout    End session',
                '',
                ' SIDEBAR BUTTONS:',
                '  MEDIA     Access multimedia content',
                '  LORE      Read world backstory',
                '  USER      Manage your account',
                '  STATS     View detailed statistics',
                '',
            ],
            'type' => 'story',
        ]);
    }
}
