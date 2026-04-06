<?php
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layout')] class extends Component {
    public $title = '';
};
?>


<div class="flex flex-col md:flex-row min-h-[calc(100dvh-4rem)] items-center justify-center px-4 gap-6">
    <x-card class="w-full max-w-sm">
        <flux:input wire:model="title" label="Title" />
    </x-card>

    <x-card class="w-full max-w-xs text-center">
        <livewire:counter />
    </x-card>

</div>
