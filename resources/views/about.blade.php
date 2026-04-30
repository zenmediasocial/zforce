@extends('layouts.public')

@section('title', 'About — Zforce')

@section('content')
<section class="py-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-16 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">About Zforce</h1>
            <p class="text-slate-500 text-sm font-mono">What this is, why it exists, and who built it</p>
        </div>
        
        <div class="space-y-12">
            <div class="bg-vortex-800/30 border border-vortex-600/20 rounded-lg p-8">
                <h2 class="text-xl font-semibold text-white mb-4 font-mono">What is Zforce?</h2>
                <p class="text-slate-400 leading-relaxed mb-4">
                    Zforce is an adaptive learning platform disguised as a time-travel narrative. 
                    Children interact with an AI commander (ZETA-7) from a fictional future who trains them 
                    through story-driven missions that adapt to their age, interests, and learning style.
                </p>
                <p class="text-slate-400 leading-relaxed">
                    The "vortex" is our metaphor for the unstable, sometimes slow, always-magical connection 
                    between a child and an AI that genuinely knows them. Every session builds a profile. 
                    Every answer shapes the next mission.
                </p>
            </div>
            
            <div class="bg-vortex-800/30 border border-vortex-600/20 rounded-lg p-8">
                <h2 class="text-xl font-semibold text-white mb-4 font-mono">How It Works</h2>
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-vortex-500/20 rounded flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-vortex-300 font-mono font-bold text-sm">1</span>
                        </div>
                        <div>
                            <h3 class="text-white font-medium mb-1">Recruitment</h3>
                            <p class="text-slate-500 text-sm">The child connects to the terminal. ZETA-7 introduces the war, the Pattern, and their role in saving humanity.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-vortex-500/20 rounded flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-vortex-300 font-mono font-bold text-sm">2</span>
                        </div>
                        <div>
                            <h3 class="text-white font-medium mb-1">Assessment</h3>
                            <p class="text-slate-500 text-sm">The AI learns their age, interests (mathematics, stories, building, discovery), and assigns a faction class.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-vortex-500/20 rounded flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-vortex-300 font-mono font-bold text-sm">3</span>
                        </div>
                        <div>
                            <h3 class="text-white font-medium mb-1">Missions</h3>
                            <p class="text-slate-500 text-sm">Adaptive missions pulled from the Archive — some hand-crafted, some AI-generated — that teach through narrative.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-vortex-500/20 rounded flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-vortex-300 font-mono font-bold text-sm">4</span>
                        </div>
                        <div>
                            <h3 class="text-white font-medium mb-1">Publication</h3>
                            <p class="text-slate-500 text-sm">Completed story arcs become Chronicles — blog posts that inspire other operators and document the journey.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-vortex-800/30 border border-vortex-600/20 rounded-lg p-8">
                <h2 class="text-xl font-semibold text-white mb-4 font-mono">The Tech Stack</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-slate-400">
                        <span class="text-vortex-300 font-mono">Backend:</span> Laravel 10, PostgreSQL
                    </div>
                    <div class="text-slate-400">
                        <span class="text-vortex-300 font-mono">Frontend:</span> Livewire, Tailwind CSS
                    </div>
                    <div class="text-slate-400">
                        <span class="text-vortex-300 font-mono">AI:</span> OpenRouter (adaptive model routing)
                    </div>
                    <div class="text-slate-400">
                        <span class="text-vortex-300 font-mono">Automation:</span> n8n workflows
                    </div>
                    <div class="text-slate-400">
                        <span class="text-vortex-300 font-mono">Admin:</span> Filament
                    </div>
                    <div class="text-slate-400">
                        <span class="text-vortex-300 font-mono">Auth:</span> Laravel Sanctum, Spatie Permissions
                    </div>
                </div>
            </div>
            
            <div class="text-center py-8">
                <p class="text-slate-600 text-sm font-mono mb-4">
                    Built with the belief that the best learning feels like an adventure.
                </p>
                <a href="{{ route('terminal') }}" class="inline-block px-8 py-4 bg-gradient-to-r from-vortex-500 to-vortex-400 rounded-lg font-mono font-semibold text-white hover:shadow-lg hover:shadow-vortex-500/25 transition-all">
                    ENTER THE TERMINAL
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
