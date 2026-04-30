@extends('layouts.public')

@section('title', 'The Lore — Zforce')

@section('content')
<section class="py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-16 text-center">
            <div class="flex items-center justify-center space-x-2 mb-4">
                <div class="w-2 h-2 rounded-full bg-signal-amber"></div>
                <span class="text-xs font-mono text-signal-amber uppercase tracking-widest">Classified</span>
            </div>
            <h1 class="text-4xl font-bold text-white mb-4">The Lore</h1>
            <p class="text-slate-500 text-sm font-mono max-w-2xl mx-auto">
                Fragments of truth recovered from the temporal vortex. 
                The following transmissions have been declassified for operator review.
            </p>
        </div>
        
        <div class="space-y-8">
            @forelse($entries as $entry)
            <div class="bg-vortex-800/30 border border-vortex-600/20 rounded-lg p-6 md:p-8">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-mono text-vortex-400">TRANSMISSION LOG</span>
                    <span class="text-xs font-mono text-slate-600">{{ $entry->slug }}</span>
                </div>
                
                <h2 class="text-xl font-semibold text-white mb-4">{{ $entry->title ?? 'Untitled Transmission' }}</h2>
                
                <div class="space-y-1 mb-4">
                    @foreach($entry->content as $line)
                        @if(trim($line) === '')
                        <div class="h-2"></div>
                        @else
                        <p class="text-slate-300 text-sm font-mono">{{ $line }}</p>
                        @endif
                    @endforeach
                </div>
                
                @if($entry->narrative_beats)
                <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-vortex-600/20">
                    @foreach($entry->narrative_beats as $beat)
                    <span class="px-2 py-1 bg-vortex-500/10 text-vortex-300 text-xs font-mono rounded">{{ $beat }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-24">
                <div class="text-6xl mb-4">🔒</div>
                <div class="text-slate-600 font-mono text-sm mb-2">[ACCESS DENIED]</div>
                <p class="text-slate-500 max-w-md mx-auto">
                    Lore fragments are classified. Complete training missions to unlock backstory transmissions.
                </p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
