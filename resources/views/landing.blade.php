@extends('layouts.public')

@section('title', 'Zforce — The Future is Real')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-vortex-700/40 via-vortex-900 to-vortex-900"></div>
    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 1px 1px, rgba(99,102,241,0.15) 1px, transparent 0); background-size: 40px 40px;"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-vortex-500/10 border border-vortex-400/20 mb-8">
            <div class="w-2 h-2 rounded-full bg-signal-green pulse-signal"></div>
            <span class="text-xs font-mono text-vortex-300 uppercase tracking-widest">Vortex Online</span>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-bold mb-6 tracking-tight">
            <span class="text-white">The war is</span>
            <span class="static-text"> real.</span>
        </h1>
        
        <p class="text-xl md:text-2xl text-slate-400 max-w-3xl mx-auto mb-4 font-light">
            You are recruited to Zforce — by <span class="text-vortex-300 font-medium">Future You</span>.
            A temporal training platform designed to prepare humanity for the Pattern Collapse of 2047.
        </p>
        
        <p class="text-sm text-slate-500 font-mono mb-12">
            Every answer shapes the training. Every pattern solved saves lives.
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('terminal') }}" class="group relative px-8 py-4 bg-gradient-to-r from-vortex-500 to-vortex-400 rounded-lg font-mono font-semibold text-white hover:shadow-lg hover:shadow-vortex-500/25 transition-all overflow-hidden">
                <span class="relative z-10">ENTER THE TERMINAL</span>
                <div class="absolute inset-0 bg-gradient-to-r from-vortex-400 to-signal-cyan opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </a>
            <a href="{{ route('chronicles') }}" class="px-8 py-4 border border-vortex-500/30 rounded-lg font-mono text-vortex-300 hover:border-vortex-400/50 hover:bg-vortex-500/10 transition-all">
                READ THE CHRONICLES
            </a>
        </div>
        
        <!-- Stats -->
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-3xl mx-auto">
            <div class="text-center">
                <div class="text-3xl font-mono font-bold text-white">{{ $stats['operators_recruited'] ?? 0 }}</div>
                <div class="text-xs text-slate-500 font-mono uppercase tracking-wider mt-1">Operators</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-mono font-bold text-signal-cyan">{{ $stats['missions_completed'] ?? 0 }}</div>
                <div class="text-xs text-slate-500 font-mono uppercase tracking-wider mt-1">Missions</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-mono font-bold text-signal-green">{{ $stats['transmissions_sent'] ?? 0 }}</div>
                <div class="text-xs text-slate-500 font-mono uppercase tracking-wider mt-1">Transmissions</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-mono font-bold text-vortex-300">{{ $stats['chronicles_published'] ?? 0 }}</div>
                <div class="text-xs text-slate-500 font-mono uppercase tracking-wider mt-1">Chronicles</div>
            </div>
        </div>
    </div>
</section>

<div class="signal-line max-w-4xl mx-auto"></div>

<!-- Latest Chronicles -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h2 class="text-3xl font-bold text-white mb-2">Archive Chronicles</h2>
                <p class="text-slate-500 text-sm font-mono">Recovered transmission logs from the future</p>
            </div>
            <a href="{{ route('chronicles') }}" class="text-vortex-400 hover:text-vortex-300 text-sm font-mono transition-colors">
                VIEW ALL →
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($latestChronicles as $post)
            <a href="{{ route('chronicle.show', $post->slug) }}" class="group block bg-vortex-800/50 border border-vortex-600/20 rounded-lg p-6 hover:border-vortex-400/30 hover-glow transition-all">
                <div class="flex items-center space-x-2 mb-4">
                    <span class="px-2 py-1 bg-vortex-500/20 text-vortex-300 text-xs font-mono rounded">{{ $post->category }}</span>
                    <span class="text-slate-600 text-xs font-mono">{{ $post->published_at?->format('M d, Y') }}</span>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-vortex-300 transition-colors">{{ $post->title }}</h3>
                <p class="text-slate-500 text-sm line-clamp-3">{{ $post->excerpt }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-slate-600 font-mono">By {{ $post->author_name }}</span>
                    <span class="text-xs text-slate-600 font-mono">{{ $post->view_count }} views</span>
                </div>
            </a>
            @empty
            <div class="col-span-3 text-center py-12">
                <div class="text-slate-600 font-mono text-sm mb-2">[ARCHIVE EMPTY]</div>
                <p class="text-slate-500">No chronicles published yet. The vortex is preparing transmissions.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<div class="signal-line max-w-4xl mx-auto"></div>

<!-- Mission Library Preview -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h2 class="text-3xl font-bold text-white mb-2">Mission Library</h2>
                <p class="text-slate-500 text-sm font-mono">Training simulations from the Archive</p>
            </div>
            <a href="{{ route('missions') }}" class="text-vortex-400 hover:text-vortex-300 text-sm font-mono transition-colors">
                EXPLORE ALL →
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($featuredMissions as $mission)
            <a href="{{ route('mission.show', $mission->slug) }}" class="group block bg-vortex-800/30 border border-vortex-600/20 rounded-lg p-5 hover:border-signal-cyan/30 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono text-signal-cyan">MISSION</span>
                    <span class="text-xs font-mono text-slate-600">#{{ $mission->usage_count }}</span>
                </div>
                <h3 class="text-sm font-semibold text-white mb-2 group-hover:text-signal-cyan transition-colors">{{ $mission->title ?? 'Untitled Mission' }}</h3>
                <p class="text-xs text-slate-500 font-mono">{{ $mission->slug }}</p>
            </a>
            @empty
            <div class="col-span-4 text-center py-12">
                <div class="text-slate-600 font-mono text-sm mb-2">[NO MISSIONS AVAILABLE]</div>
                <p class="text-slate-500">The Archive is being populated. Check back soon.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 bg-gradient-to-b from-vortex-900 to-vortex-800/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">The future depends on you.</h2>
        <p class="text-slate-400 mb-8 max-w-2xl mx-auto">
            Every operator who enters the terminal strengthens the Pattern. 
            Every mission completed is a step toward preserving humanity in 2047.
        </p>
        <a href="{{ route('terminal') }}" class="inline-block px-8 py-4 bg-signal-green/20 border border-signal-green/40 rounded-lg font-mono font-semibold text-signal-green hover:bg-signal-green/30 transition-all">
            BEGIN TRAINING →
        </a>
    </div>
</section>
@endsection
