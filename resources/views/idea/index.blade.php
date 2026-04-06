<x-layout>
    <div>
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground text-sm mt-2">Capture your thoughts. Make a plan.</p>

            {{-- Restored: Your original big, bold trigger card --}}
            <x-card x-data @click="$dispatch('open-modal', 'create-idea')" x-init="@if ($errors->any()) $nextTick(() => $dispatch('open-modal', 'create-idea')) @endif" is="button"
                type="button" class="mt-10 cursor-pointer h-32 w-full text-left">
                <p>What's the idea?</p>
            </x-card>
        </header>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-2 mb-10">
            <a href="/ideas" class="btn {{ request()->has('status') ? 'btn-outlined' : '' }}">All</a>
            @foreach (App\IdeaStatus::cases() as $status)
                <a href="/ideas?status={{ $status->value }}"
                    class="btn {{ request('status') === $status->value ? '' : 'btn-outlined' }}">
                    {{ $status->label() }} <span class="text-xs pl-1">{{ $statusCounts->get($status->value) }}</span>
                </a>
            @endforeach
        </div>

        {{-- Grid: Restored to 3 columns (lg:grid-cols-3) with the nicer card style --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($ideas as $idea)
                <x-card href="{{ route('idea.show', $idea) }}" class="flex flex-col h-full p-o! overflow-hidden">
                    @if ($idea->image_path)
                        <div class="aspect-video overflow-hidden border-b border-border">
                            <img src="{{ asset('storage/' . $idea->image_path) }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="text-foreground text-lg font-bold leading-tight line-clamp-2">{{ $idea->title }}
                        </h3>

                        <div class="mt-2">
                            <x-status-label status="{{ $idea->status }}">{{ $idea->status->label() }}</x-status-label>
                        </div>

                        <div class="mt-3 flex-1">
                            @if ($idea->description)
                                <p class="text-muted-foreground text-sm line-clamp-2 prose prose-invert">
                                    {!! $idea->description !!}</p>
                            @else
                                <p class="text-muted-foreground/50 text-sm italic">No description inserted.</p>
                            @endif
                        </div>

                        @if ($idea->created_at)
                            <div class="mt-4 text-xs text-muted-foreground/70">{{ $idea->created_at->diffForHumans() }}
                            </div>
                        @endif
                    </div>
                </x-card>
            @empty
                <div class="col-span-full py-12 text-center text-muted-foreground italic">No ideas found.</div>
            @endforelse
        </div>

        <x-idea.modal />
    </div>
</x-layout>
