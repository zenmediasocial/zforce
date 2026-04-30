@extends('layouts.public')

@section('title', 'Mission Library — Zforce')

@section('content')
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <div class="flex items-center space-x-2 mb-4">
                <div class="w-2 h-2 rounded-full bg-signal-cyan"></div>
                <span class="text-xs font-mono text-signal-cyan uppercase tracking-widest">Training</span>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Mission Library</h1>
            <p class="text-slate-500 text-sm font-mono">Simulations from the Archive — ready for deployment</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($missions as $mission)
            <a href="{{ route('mission.show', $mission->slug) }}" class="group block bg-vortex-800/30 border border-vortex-600/20 rounded-lg p-6 hover:border-signal-cyan/30 hover-glow transition-all">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-mono text-signal-cyan">MISSION</span>
                    <span class="text-xs font-mono text-slate-600">{{ $mission->usage_count }}x deployed</span>
                </div>
                <h2 class="text-lg font-semibold text-white mb-2 group-hover:text-signal-cyan transition-colors">{{ $mission->title ?? 'Untitled Mission' }}</h2>
                <p class="text-xs text-slate-500 font-mono mb-4">{{ $mission->slug }}</p>
                
                @if($mission->conditions)
                <div class="flex flex-wrap gap-2">
                    @if(isset($mission->conditions['affinity']))
                    <span class="px-2 py-1 bg-vortex-500/10 text-vortex-300 text-xs font-mono rounded">{{ $mission->conditions['affinity'] }}</span>
                    @endif
                    @if(isset($mission->conditions['min_age']))
                    <span class="px-2 py-1 bg-vortex-500/10 text-vortex-300 text-xs font-mono rounded">Age {{ $mission->conditions['min_age'] }}+</span>
                    @endif
                    @if(isset($mission->conditions['phase']))
                    <span class="px-2 py-1 bg-vortex-500/10 text-vortex-300 text-xs font-mono rounded">{{ $mission->conditions['phase'] }}</span>
                    @endif
                </div>
                @endif
            </a>
            @empty
            <div class="col-span-3 text-center py-24">
                <div class="text-6xl mb-4">📦</div>
                <div class="text-slate-600 font-mono text-sm mb-2">[ARCHIVE EMPTY]</div>
                <p class="text-slate-500 max-w-md mx-auto">No missions in the Archive yet. The vortex is generating simulations.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $missions->links() }}
        </div>
    </div>
</section>
@endsection
