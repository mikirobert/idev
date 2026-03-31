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
                <x-card href="{{ route('idea.show', $idea) }}" class="flex flex-col h-full !p-0 overflow-hidden">
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
                                <p class="text-muted-foreground text-sm line-clamp-2">{{ $idea->description }}</p>
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

        {{-- Modal: Your original spacious layout with technical fixes --}}
        <x-modal name="create-idea" title="New Idea" :open="$errors->any()">
            <form x-data="{
                status: 'pending',
                newStep: '',
                steps: @js(old('steps', [])),
                newLink: '',
                links: @js(old('links', [])),
                showStepWarning: false,
                showLinkWarning: false
            }"
                @submit.prevent="
                showStepWarning = (steps.length === 0 && newStep.trim().length > 0);
                showLinkWarning = (links.length === 0 && newLink.trim().length > 0);
                if (!showStepWarning && !showLinkWarning) { $el.submit(); }
            "
                action="{{ route('idea.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-4">
                    {{-- Title --}}
                    <div>
                        <x-form.field label="Title" name="title" type="text" autofocus
                            placeholder="e.g. A New App" required />
                        <x-form.error name="title" />
                    </div>

                    {{-- Status --}}
                    <div class="space-y-2">
                        <label class="label">Status</label>
                        <div class="flex gap-x-3">
                            @foreach (App\IdeaStatus::cases() as $status)
                                <button type="button" @click="status = @js($status->value)"
                                    class="btn flex-1 h-10"
                                    :class="status === @js($status->value) ? '' : 'btn-outlined'">
                                    {{ $status->label() }}
                                </button>
                            @endforeach
                            <input type="hidden" name="status" :value="status">
                        </div>
                    </div>

                    <x-form.field label="Description" name="description" type="textarea"
                        placeholder="e.g. I want to build..." />

                    {{-- Image Input --}}
                    <div class="space-y-2">
                        <label class="label">Featured Image</label>
                        <input type="file" name="image" accept="image/*"
                            class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground hover:file:opacity-80 cursor-pointer">
                        <x-form.error name="image" />
                    </div>

                    {{-- Steps --}}
                    <fieldset class="space-y-3" x-data="{ stepEmptyError: false }">
                        <legend class="label">Steps</legend>
                        <template x-for="(step, index) in steps" :key="index">
                            <div class="flex gap-x-2 items-center">
                                <input type="text" name="steps[]" x-model="steps[index]" class="input" readonly>
                                <button type="button" @click="steps.splice(index, 1)" class="form-muted-icon">
                                    <x-icons.close />
                                </button>
                            </div>
                        </template>

                        <div class="flex gap-x-2 items-center">
                            <input x-model="newStep" @input="stepEmptyError = false; showStepWarning = false" type="text"
                                placeholder="What needs to be done?" class="input flex-1">
                            <button type="button" class="form-muted-icon"
                                @click="if(newStep.trim()){ steps.push(newStep.trim()); newStep=''; stepEmptyError=false; showStepWarning=false; } else { stepEmptyError = true }">
                                <x-icons.close class="rotate-45" />
                            </button>
                        </div>
                        {{-- Inline Empty Error --}}
                        <p x-show="stepEmptyError" x-transition class="text-xs text-red-500 font-medium">Please type a
                            step before adding it.</p>
                        <p x-show="showStepWarning" x-transition class="text-xs text-amber-600 mt-1 font-medium">
                            You forgot to click the + button to add this step!
                        </p>
                    </fieldset>

                    {{-- Links --}}
                    <fieldset class="space-y-3" x-data="{ linkEmptyError: false }">
                        <legend class="label">Links</legend>
                        <template x-for="(link, index) in links" :key="index">
                            <div class="flex gap-x-2 items-center">
                                <input type="text" name="links[]" x-model="links[index]" class="input" readonly>
                                <button type="button" @click="links.splice(index, 1)" class="form-muted-icon">
                                    <x-icons.close />
                                </button>
                            </div>
                        </template>

                        <div class="flex gap-x-2 items-center">
                            <input x-model="newLink" @input="linkEmptyError = false; showLinkWarning = false" type="url" x-ref="lIn"
                                placeholder="http://..." class="input flex-1">
                            <button type="button" class="form-muted-icon"
                                @click="if($refs.lIn.checkValidity() && newLink.trim()){ links.push(newLink.trim()); newLink=''; linkEmptyError=false; showLinkWarning=false; } else { if(!newLink.trim()) { linkEmptyError = true } else { $refs.lIn.reportValidity() } }">
                                <x-icons.close class="rotate-45" />
                            </button>
                        </div>
                        {{-- Inline Empty Error --}}
                        <p x-show="linkEmptyError" x-transition class="text-xs text-red-500 font-medium">Please enter
                            a URL before adding it.</p>
                        <p x-show="showLinkWarning" x-transition class="text-xs text-amber-600 mt-1 font-medium">
                            You forgot to click the + button to add this link!
                        </p>
                    </fieldset>

                    <div class="flex gap-x-5 pt-2">
                        <button type="button" @click="$dispatch('close-modal')">Cancel</button>
                        <button type="submit" class="btn">Create</button>
                    </div>
                </div>
            </form>
        </x-modal>
    </div>
</x-layout>
