@extends('layouts.public')

@section('title', ($mission->title ?? 'Mission') . ' — Zforce')

@section('content')
<section class="py-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('missions') }}" class="text-xs font-mono text-signal-cyan hover:text-signal-green transition-colors mb-6 inline-block">← BACK TO LIBRARY</a>
        
        <div class="bg-vortex-800/30 border border-vortex-600/20 rounded-lg p-8 md:p-12">
            <div class="flex items-center justify-between mb-6">
                <span class="text-xs font-mono text-signal-cyan">MISSION BRIEFING</span>
                <span class="text-xs font-mono text-slate-600">{{ $mission->slug }}</span>
            </div>
            
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-6">{{ $mission->title ?? 'Untitled Mission' }}</h1>
            
            <div class="space-y-2 mb-8">
                @foreach($mission->content as $line)
                <p class="text-slate-300 font-mono text-sm">{{ $line }}</p>
                @endforeach
            </div>
            
            @if($mission->choices)
            <div class="space-y-3">
                <p class="text-xs text-slate-500 font-mono uppercase tracking-wider mb-2">Options</p>
                @foreach($mission->choices as $choice)
                <div class="flex items-center space-x-3 bg-vortex-900/50 border border-vortex-600/20 rounded px-4 py-3">
                    <span class="text-signal-cyan font-mono font-bold">[{{ $choice['key'] }}]</span>
                    <span class="text-slate-300 text-sm">{{ $choice['label'] }}</span>
                </div>
                @endforeach
            </div>
            @endif
            
            <div class="mt-8 pt-8 border-t border-vortex-600/20 flex items-center justify-between">
                <span class="text-xs text-slate-600 font-mono">Deployed {{ $mission->usage_count }} times</span>
                <a href="{{ route('terminal') }}" class="px-6 py-3 bg-signal-green/20 border border-signal-green/40 rounded text-sm font-mono text-signal-green hover:bg-signal-green/30 transition-all">
                    ENTER TERMINAL TO PLAY →
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
