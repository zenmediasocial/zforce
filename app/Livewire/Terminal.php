<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\StoryPage;
use App\Services\Vortex\VortexTransmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Terminal extends Component
{
    public array $lines = [];
    public string $input = '';
    public ?string $currentPage = null;
    public bool $isTyping = false;
    public array $history = [];
    public int $historyIndex = -1;
    public bool $booted = false;
    public array $bootSequence = [];
    public array $menuItems = [];
    public bool $vortexMode = false;
    public array $vortexChoices = [];
    public ?string $vortexState = null;

    protected $listeners = ['lines-typed' => 'addTypedLines'];

    public function mount(): void
    {
        $this->bootSequence = [
            ['text' => 'BIOS DATE 04/28/26 14:58:22 VER 1.0.2', 'class' => 'dim'],
            ['text' => 'CPU: Quantum Learning Core Processor', 'class' => 'dim'],
            ['text' => 'RAM TEST: 640K OK... 1048576K OK', 'class' => 'dim'],
            ['text' => '', 'class' => ''],
            ['text' => 'LOADING TERMINAL LEARNING SYSTEM...', 'class' => 'bright'],
            ['text' => 'WARNING: UNAUTHORIZED ACCESS PROHIBITED', 'class' => 'warning'],
            ['text' => '', 'class' => ''],
            ['text' => 'Initializing educational modules...', 'class' => 'dim'],
            ['text' => 'Connecting to knowledge database...', 'class' => 'dim'],
            ['text' => 'User authentication... OK', 'class' => 'dim'],
        ];
    }

    public function bootComplete(): void
    {
        $this->booted = true;
        $this->showMenu();
    }

    public function addTypedLines(array $lines): void
    {
        foreach ($lines as $line) {
            $this->lines[] = $line;
        }
    }

    public function processCommand(): void
    {
        if ($this->isTyping) {
            return;
        }

        $cmd = trim(strtolower($this->input));
        $this->history[] = $this->input;
        $this->historyIndex = -1;

        // Echo command
        $this->addLine("C:\\> {$this->input}", 'bright');
        $this->input = '';

        if ($cmd === '') {
            return;
        }

        match (true) {
            $cmd === 'help' => $this->showHelp(),
            $cmd === 'back' || $cmd === '..' || $cmd === 'cd ..' => $this->showMenu(),
            $cmd === 'clear' || $cmd === 'cls' => $this->clearScreen(),
            $cmd === 'logout' => $this->logout(),
            $cmd === 'play' => $this->navigate('menu-play'),
            $cmd === 'stats' => $this->dispatch('open-panel', panel: 'stats'),
            $cmd === 'home' => $this->showMenu(),
            $cmd === 'vortex' || $cmd === 'chat' || $cmd === 'connect' => $this->enterVortex(),
            is_numeric($cmd) => $this->handleNumberSelection((int) $cmd),
            $this->vortexMode => $this->handleVortexMessage($this->input),
            default => $this->handleUnknownCommand($cmd),
        };
    }

    public function navigate(string $slug): void
    {
        $page = StoryPage::where('slug', $slug)->first();

        if (!$page) {
            $this->addLine("ERROR: Page \"{$slug}\" not found.", 'warning');
            $this->addLine('');

            return;
        }

        $this->currentPage = $slug;

        $pageLines = [
            '',
            "C:\\> open {$page->title}",
            '',
            ...($page->content ?? []),
            '',
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
            'Type "back" to return to menu',
            '',
        ];

        $this->dispatch('type-lines', lines: $pageLines);
    }

    public function historyUp(): void
    {
        if ($this->historyIndex === -1 && count($this->history) > 0) {
            $this->historyIndex = count($this->history) - 1;
            $this->input = $this->history[$this->historyIndex];
        } elseif ($this->historyIndex > 0) {
            $this->historyIndex--;
            $this->input = $this->history[$this->historyIndex];
        }
    }

    public function historyDown(): void
    {
        if ($this->historyIndex >= 0) {
            $this->historyIndex++;
            if ($this->historyIndex >= count($this->history)) {
                $this->historyIndex = -1;
                $this->input = '';
            } else {
                $this->input = $this->history[$this->historyIndex];
            }
        }
    }

    public function showMenu(): void
    {
        $this->currentPage = null;

        $menuLines = [
            '',
            'TERMINAL LEARNING SYSTEM v1.0',
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
            '',
            '  [1] Play - Learning Modules',
            '  [2] Stats - View Progress',
            '  [3] Backstory - World Lore',
            '  [4] Account - Profile Settings',
            '  [5] Help - Command List',
            '  [6] Vortex - Secure Command Channel',
            '',
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
            '',
        ];

        $this->dispatch('type-lines', lines: $menuLines);
    }

    private function showHelp(): void
    {
        $helpLines = [
            '',
            'AVAILABLE COMMANDS',
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━',
            '  [1-6]     Navigate menu',
            '  back      Return to main menu',
            '  help      Show this message',
            '  clear     Clear screen',
            '  play      Enter learning mode',
            '  stats     View statistics',
            '  vortex    Open secure command channel',
            '  logout    End session',
            '',
            ' SIDEBAR: MEDIA | LORE | USER | STATS',
            '',
        ];

        $this->dispatch('type-lines', lines: $helpLines);
    }

    private function clearScreen(): void
    {
        $this->lines = [];
    }

    private function logout(): void
    {
        Auth::logout();
        $this->redirectRoute('login');
    }

    private function handleNumberSelection(int $num): void
    {
        $mapping = [
            1 => 'menu-play',
            2 => 'stats',
            3 => 'backstory',
            4 => 'account',
            5 => 'menu-help',
            6 => 'vortex',
        ];

        if (isset($mapping[$num])) {
            if ($mapping[$num] === 'stats') {
                $this->dispatch('open-panel', panel: 'stats');
            } elseif ($mapping[$num] === 'backstory') {
                $this->dispatch('open-panel', panel: 'backstory');
            } elseif ($mapping[$num] === 'account') {
                $this->dispatch('open-panel', panel: 'account');
            } elseif ($mapping[$num] === 'vortex') {
                $this->enterVortex();
            } else {
                $this->navigate($mapping[$num]);
            }
        } else {
            $this->addLine("Invalid selection: {$num}", 'warning');
            $this->addLine('');
        }
    }

    private function handleUnknownCommand(string $cmd): void
    {
        $this->addLine("'{$cmd}' is not recognized as an internal or external command.", 'warning');
        $this->addLine('Type "help" for available commands.');
        $this->addLine('');
    }

    private function addLine(string $text, string $class = ''): void
    {
        $this->lines[] = ['text' => $text, 'class' => $class];
    }

    private function enterVortex(): void
    {
        $this->vortexMode = true;
        $this->vortexChoices = [];
        $this->addLine('', '');
        $this->addLine('[INITIATING TEMPORAL LINK...]', 'vortex');
        $this->addLine('[SCANNING TEMPORAL FREQUENCIES...]', 'vortex-dim');
        $this->addLine('', '');

        $user = Auth::user();
        if (!$user) {
            $this->addLine('[ERROR: NO AUTHENTICATED OPERATOR]', 'warning');
            $this->vortexMode = false;
            return;
        }

        $transmission = app(VortexTransmission::class);
        $result = $transmission->send($user, 'connect');

        $this->renderVortexResponse($result);
    }

    private function handleVortexMessage(string $message): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->addLine('[VORTEX ERROR: OPERATOR LOST]', 'warning');
            $this->vortexMode = false;
            return;
        }

        // Check if input matches a vortex choice
        $choiceKey = trim($message);
        if (is_numeric($choiceKey) && !empty($this->vortexChoices)) {
            foreach ($this->vortexChoices as $choice) {
                if ($choice['key'] === $choiceKey) {
                    $message = $choice['label'];
                    break;
                }
            }
        }

        $this->vortexChoices = [];

        $transmission = app(VortexTransmission::class);
        $result = $transmission->send($user, $message);

        $this->renderVortexResponse($result);
    }

    private function renderVortexResponse(\App\Services\Vortex\TransmissionResult $result): void
    {
        $this->vortexState = $result->vortexState;

        // Vortex state indicator
        $stateClass = match ($result->vortexState) {
            \App\Services\Vortex\VortexState::STABLE => 'vortex-stable',
            \App\Services\Vortex\VortexState::UNSTABLE => 'vortex-unstable',
            \App\Services\Vortex\VortexState::DOWN => 'vortex-down',
            \App\Services\Vortex\VortexState::REALIGNING => 'vortex-realigning',
            default => 'vortex',
        };

        $this->addLine('', '');
        $this->addLine("[VORTEX: {$result->vortexState}]", $stateClass);

        if ($result->hasCountdown()) {
            $this->addLine("[REALIGNING: {$result->countdownDuration}s]", 'vortex-countdown');
        }

        $this->addLine('', '');

        // Content lines
        $lines = explode("\n", $result->content);
        foreach ($lines as $line) {
            $this->addLine($line, 'vortex-message');
        }

        // Choices
        if ($result->choices) {
            $this->addLine('', '');
            $this->vortexChoices = $result->choices;
            foreach ($result->choices as $choice) {
                $this->addLine("[{$choice['key']}] {$choice['label']}", 'vortex-choice');
            }
        }

        // Source indicator (subtle)
        $sourceLabel = $result->isRealtime() ? 'REAL-TIME' : 'ARCHIVE';
        $this->addLine('', '');
        $this->addLine("[SOURCE: {$sourceLabel}]", 'vortex-dim');

        if ($result->model) {
            $this->addLine("[MODEL: {$result->model}]", 'vortex-dim');
        }

        $this->addLine('', '');
    }

    public function render()
    {
        return view('livewire.terminal');
    }
}
