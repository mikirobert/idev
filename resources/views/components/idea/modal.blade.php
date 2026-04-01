@props(['idea' => new App\Models\Idea()])

<x-modal name="{{ $idea->exists ? 'edit-idea' : 'create-idea' }}" title="{{ $idea->exists ? 'Edit Idea' : 'New Idea' }}"
    :open="$errors->any()">

    <form x-data="{
        status: @js(old('status', $idea->status?->value ?? App\IdeaStatus::cases()[0]->value)),
        newStep: '',
        steps: @js(
    old(
        'steps',
        $idea->steps
            ->map(
                fn($s) => [
                    'id' => $s->id,
                    'description' => $s->description,
                    'completed' => $s->completed ? 1 : 0,
                ],
            )
            ->toArray(),
    ),
),
        newLink: '',
        links: @js(old('links', (array) ($idea->links ?? []))),
        stepEmptyError: false,
        linkEmptyError: false,
        showStepWarning: false,
        showLinkWarning: false,

        submitForm() {
            this.showStepWarning = this.newStep.trim().length > 0;
            this.showLinkWarning = this.newLink.trim().length > 0;

            if (!this.showStepWarning && !this.showLinkWarning) {
                if (this.$el.checkValidity()) {
                    this.$el.submit();
                } else {
                    this.$el.reportValidity();
                }
            }
        }
    }" @submit.prevent="submitForm()"
        action="{{ $idea->exists ? route('idea.update', $idea) : route('idea.store') }}" method="POST"
        enctype="multipart/form-data">

        @csrf
        @if ($idea->exists)
            @method('PATCH')
        @endif

        <div class="space-y-4">


            {{-- Title --}}
            <div>
                <x-form.field label="Title" name="title" type="text" autofocus required :value="$idea->title" />
                <x-form.error name="title" />
            </div>

            {{-- Status --}}
            <div class="space-y-2">
                <label class="label">Status</label>
                <div class="flex gap-x-3">
                    @foreach (App\IdeaStatus::cases() as $statusEnum)
                        <button type="button" @click="status = @js($statusEnum->value)" class="btn flex-1 h-10"
                            :class="status === @js($statusEnum->value) ? '' : 'btn-outlined'">
                            {{ $statusEnum->label() }}
                        </button>
                    @endforeach
                    <input type="hidden" name="status" :value="status">
                </div>
                <x-form.error name="status" />
            </div>

            <x-form.field label="Description" name="description" type="textarea" :value="$idea->description" />

            {{-- Image --}}
            <div class="space-y-2">
                @if ($idea->image_path)
                    <div class="space-y-2">
                        <img src="{{ asset('storage/' . $idea->image_path) }}"
                            class="w-full h-48 object-cover rounded-lg">
                        <button form="delete-image-form" type="submit" class="btn btn-outlined h-10 w-full">Remove
                            Image</button>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*">
                <x-form.error name="image" />
            </div>

            {{-- Steps Loop --}}
            <fieldset class="space-y-3">
                <legend class="label">Steps</legend>
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex gap-x-2 items-center">
                        <input type="text" :name="`steps[${index}][description]`" x-model="step.description"
                            class="input" readonly>
                        <input type="hidden" :name="`steps[${index}][id]`" :value="step.id">
                        <input type="hidden" :name="`steps[${index}][completed]`" :value="step.completed">
                        <button type="button" @click="steps.splice(index, 1)"
                            class="form-muted-icon"><x-icons.close /></button>
                    </div>
                </template>

                <div class="flex gap-x-2 items-center">
                    <input x-model="newStep" {{-- Clear error as they type --}} @input="stepEmptyError = false"
                        @keydown.enter.prevent="if(newStep.trim()){ steps.push({id: null, description: newStep.trim(), completed: 0}); newStep=''; stepEmptyError = false; showStepWarning = false; } else { stepEmptyError = true }"
                        type="text" class="input flex-1" placeholder="Add a step...">
                    <button type="button"
                        @click="if(newStep.trim()){ steps.push({id: null, description: newStep.trim(), completed: 0}); newStep=''; stepEmptyError = false; showStepWarning = false; } else { stepEmptyError = true }">
                        <x-icons.close class="rotate-45" />
                    </button>
                </div>
                <p x-show="stepEmptyError" x-cloak class="text-xs text-red-500">Please type a step before adding it.</p>
                <p x-show="showStepWarning" x-cloak class="text-xs text-amber-600">Click the + button to add the step!
                </p>
            </fieldset>

            {{-- Links --}}
            <fieldset class="space-y-3">
                <legend class="label">Links</legend>
                <template x-for="(link, index) in links" :key="index">
                    <div class="flex gap-x-2 items-center">
                        <input type="text" name="links[]" x-model="links[index]" class="input" readonly>
                        <button type="button" @click="links.splice(index, 1)"
                            class="form-muted-icon"><x-icons.close /></button>
                    </div>
                </template>

                <div class="flex gap-x-2 items-center">
                    <input x-model="newLink" x-ref="lIn" type="url" class="input flex-1"
                        placeholder="https://..." @input="linkEmptyError = false"
                        @keydown.enter.prevent="if($refs.lIn.checkValidity() && newLink.trim()){ links.push(newLink.trim()); newLink=''; linkEmptyError = false; showLinkWarning = false; } else { linkEmptyError = true }">
                    <button type="button"
                        @click="if($refs.lIn.checkValidity() && newLink.trim()){ links.push(newLink.trim()); newLink=''; linkEmptyError = false; showLinkWarning = false; } else { linkEmptyError = true }">
                        <x-icons.close class="rotate-45" />
                    </button>
                </div>
                {{-- Fixed variable name here to match your x-data --}}
                <p x-show="linkEmptyError" x-cloak class="text-xs text-red-500">Please type a URL before adding it.</p>
                <p x-show="showLinkWarning" x-cloak class="text-xs text-amber-600">Click the + button to add the link!
                </p>
            </fieldset>

            <div class="flex gap-x-5 pt-2">
                <button type="button" @click="$dispatch('close-modal')">Cancel</button>
                <button type="submit" class="btn">{{ $idea->exists ? 'Update' : 'Create' }}</button>
            </div>
        </div>
    </form>

    @if ($idea->image_path)
        <form method="POST" action="{{ route('ideaImage.destroy', $idea) }}" id="delete-image-form" class="hidden">
            @csrf @method('DELETE')
        </form>
    @endif
</x-modal>
