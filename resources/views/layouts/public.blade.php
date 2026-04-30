<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Zforce — The Future is Real')</title>
    <meta name="description" content="Zforce is a temporal training platform. The war is real. Humanity must survive. Train the Pattern.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        mono: ['JetBrains Mono', 'monospace'],
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        vortex: {
                            900: '#0a0a0f',
                            800: '#12121a',
                            700: '#1a1a2e',
                            600: '#252542',
                            500: '#3a3a6e',
                            400: '#6366f1',
                            300: '#818cf8',
                            200: '#a5b4fc',
                            100: '#c7d2fe',
                        },
                        signal: {
                            green: '#22c55e',
                            amber: '#f59e0b',
                            red: '#ef4444',
                            cyan: '#06b6d4',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0a0a0f; color: #e2e8f0; }
        .vortex-glow { box-shadow: 0 0 60px rgba(99, 102, 241, 0.15); }
        .signal-line { background: linear-gradient(90deg, transparent, #6366f1, transparent); height: 1px; }
        .terminal-border { border: 1px solid rgba(99, 102, 241, 0.2); }
        .hover-glow:hover { box-shadow: 0 0 30px rgba(99, 102, 241, 0.2); }
        .static-text { background: linear-gradient(90deg, #6366f1, #06b6d4, #22c55e); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        @keyframes pulse-signal {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .pulse-signal { animation: pulse-signal 2s ease-in-out infinite; }
    </style>
</head>
<body class="font-sans antialiased min-h-screen">
    <nav class="fixed top-0 w-full z-50 bg-vortex-900/90 backdrop-blur-md border-b border-vortex-600/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-vortex-400 to-signal-cyan rounded flex items-center justify-center">
                        <span class="text-white font-mono font-bold text-sm">Z</span>
                    </div>
                    <span class="font-mono font-bold text-lg tracking-wider text-white">ZFORCE</span>
                </a>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('chronicles') }}" class="text-sm text-slate-400 hover:text-signal-cyan transition-colors">Chronicles</a>
                    <a href="{{ route('missions') }}" class="text-sm text-slate-400 hover:text-signal-cyan transition-colors">Missions</a>
                    <a href="{{ route('lore') }}" class="text-sm text-slate-400 hover:text-signal-cyan transition-colors">Lore</a>
                    <a href="{{ route('about') }}" class="text-sm text-slate-400 hover:text-signal-cyan transition-colors">About</a>
                    <a href="{{ route('terminal') }}" class="px-4 py-2 bg-vortex-500/30 border border-vortex-400/40 rounded text-sm font-mono text-vortex-300 hover:bg-vortex-500/50 transition-all">
                        ENTER TERMINAL
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-16">
        @yield('content')
    </main>

    <footer class="border-t border-vortex-600/30 bg-vortex-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-vortex-400 to-signal-cyan rounded flex items-center justify-center">
                            <span class="text-white font-mono font-bold text-sm">Z</span>
                        </div>
                        <span class="font-mono font-bold text-lg text-white">ZFORCE</span>
                    </div>
                    <p class="text-slate-500 text-sm max-w-md">
                        A temporal training platform from 2047. The war is real. The Pattern must be preserved. 
                        Humanity depends on the operators we train today.
                    </p>
                </div>
                <div>
                    <h4 class="font-mono font-semibold text-sm text-white mb-4">Platform</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('chronicles') }}" class="text-slate-500 hover:text-signal-cyan text-sm transition-colors">Chronicles</a></li>
                        <li><a href="{{ route('missions') }}" class="text-slate-500 hover:text-signal-cyan text-sm transition-colors">Mission Library</a></li>
                        <li><a href="{{ route('lore') }}" class="text-slate-500 hover:text-signal-cyan text-sm transition-colors">The Lore</a></li>
                        <li><a href="{{ route('terminal') }}" class="text-slate-500 hover:text-signal-cyan text-sm transition-colors">Terminal</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-mono font-semibold text-sm text-white mb-4">Signal Status</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full bg-signal-green pulse-signal"></div>
                            <span class="text-slate-500">Vortex: Stable</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full bg-signal-cyan"></div>
                            <span class="text-slate-500">Archive: Online</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full bg-signal-amber"></div>
                            <span class="text-slate-500">Training: Active</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-vortex-600/20 text-center">
                <p class="text-slate-600 text-xs font-mono">
                    TEMPORAL INTERFACE v2.0.47 — TRANSMITTED FROM THE FUTURE — 
                    <span class="text-vortex-400">ZFORCE.ARMY</span>
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
