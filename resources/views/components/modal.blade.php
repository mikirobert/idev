@props(['name', 'title', 'open' => false])
<div x-data="{ show: @js($open), name: @js($name) }" x-show="show"
    @open-modal.window="if($event.detail === name) show = true;"
    @keydown.escape.window="show = false" @close-modal="show = false"
    class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 px-4 py-8 backdrop-blur-xs"
    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4 -translate-x-4"
    x-transition:leave="ease-in duration-150" x-transition:leave-end="opacity-0 -translate-y-4 -translate-x-4"
    style="display: none" role="dialog" aria-modal="true" aria-labelledby="modal-{{ $name }}-title"
    :aria-hidden="!show" tabindex="-1">
    <x-card @click.away="show=false" class="shadow-xl max-w-xl w-full">
        <div class="flex justify-between items-center">
            <h2 id="modal-{{ $name }}-title" class="text-xl font-bold">{{ $title }}</h2>

            <button aria-label="Close modal" @click="show=false">
                <x-icons.close />
                </button>
            </div>

        <div class="mt-6">
            <p>{{ $slot }}</p>
            </div>
        </x-card>
</div>
