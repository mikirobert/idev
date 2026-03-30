<x-layout>
    <div class="py-8 max-w-4xl mx-auto">
        <div class="flex justify-between">
            <a href="{{ route('idea.index') }}" class="flex items-center gap-x-2 text-sm font-medium">
                <x-icons.arrow-back />
                Back to Ideas
            </a>

            <div class="gap-x-2 flex items-center">
                <button class="btn btn-outlined">
                    <x-icons.external />
                    Edit Idea
                </button>
                <form method="POST" action="{{ route('idea.destroy', $idea) }}">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-outlined text-red-500">Delete</button>
                </form>
            </div>
        </div>


        <div class="mt-8 space-y-6">
            <h1 class="font-bold text-3xl">{{ $idea->title }}</h1>

            <div class="mt-2 flex gap-x-3 items-center">
                <x-status-label :status="$idea->status->value"> {{ $idea->status->label() }}</x-status-label>

                <div class="text-muted-foreground text-sm">
                    {{ $idea->created_at->diffForHumans() }}
                </div>
            </div>

            <x-card class="mt-5">
                <div class="text-foreground max-w-none cursor-pointer">{{ $idea->description }}</div>
            </x-card>

            @if($idea->links->count())
            <div>
                <h3 class="font-bold text-xl mt-5">Links</h3>

                <div class="mt-3 space-y-2">
                    @foreach ($idea->links as $link)
                    <x-card :href="$link" class="p-0"> {{-- p-0 to prevent internal card padding from breaking flex --}}
                        <div class="flex items-center gap-x-3 text-primary font-medium">
                            <div class="shrink-0 flex items-center">
                                <x-icons.external />
                            </div>
                            <span class="truncate">
                                {{ $link }}
                            </span>
                        </div>
                    </x-card>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layout>
