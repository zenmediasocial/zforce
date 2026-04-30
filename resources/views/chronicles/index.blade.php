@extends('layouts.public')

@section('title', 'Archive Chronicles — Zforce')

@section('content')
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <div class="flex items-center space-x-2 mb-4">
                <div class="w-2 h-2 rounded-full bg-vortex-400"></div>
                <span class="text-xs font-mono text-vortex-400 uppercase tracking-widest">Archive</span>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Chronicles</h1>
            <p class="text-slate-500 text-sm font-mono">Recovered transmission logs from Timeline Theta-4</p>
        </div>

        <div class="space-y-6">
            @forelse($posts as $post)
            <a href="{{ route('chronicle.show', $post->slug) }}" class="group block bg-vortex-800/30 border border-vortex-600/20 rounded-lg p-6 md:p-8 hover:border-vortex-400/30 transition-all">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="px-2 py-1 bg-vortex-500/20 text-vortex-300 text-xs font-mono rounded">{{ $post->category }}</span>
                            <span class="text-slate-600 text-xs font-mono">{{ $post->published_at?->format('F j, Y') }}</span>
                        </div>
                        <h2 class="text-xl font-semibold text-white mb-2 group-hover:text-vortex-300 transition-colors">{{ $post->title }}</h2>
                        <p class="text-slate-500 text-sm">{{ $post->excerpt }}</p>
                    </div>
                    <div class="flex items-center space-x-6 text-xs font-mono text-slate-600">
                        <span>By {{ $post->author_name }}</span>
                        <span>{{ $post->view_count }} views</span>
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-24">
                <div class="text-6xl mb-4">📡</div>
                <div class="text-slate-600 font-mono text-sm mb-2">[SIGNAL FRAGMENT]</div>
                <p class="text-slate-500 max-w-md mx-auto">The Archive is silent. No chronicles have been recovered yet. The vortex may realign soon.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
</section>
@endsection
