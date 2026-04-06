<?php
use Livewire\Volt\Component;
new class extends Component {
    public $post;
    public function with()
    {
        sleep(1);
        return ['post' => $this->post];
    }
}; ?>

<div>
    @placeholder
        <flux:card class="space-y-4 shadow-sm">
            <flux:skeleton class="h-5 w-2/3" />
            <flux:skeleton class="h-3 w-full" />
            <flux:skeleton class="h-3 w-full" />
        </flux:card>
    @endplaceholder

    <flux:card class="flex flex-col justify-between h-full shadow-sm">
        <div>
            <flux:heading size="lg">{{ $post->title }}</flux:heading>
            <flux:text size="sm" class="mt-1">
                {{ is_string($post->created_at) ? $post->created_at : $post->created_at->format('M d, Y') }}</flux:text>
            <flux:text class="mt-4 line-clamp-3">{{ $post->content }}</flux:text>
        </div>

        <div class="mt-6 flex justify-between items-center">
            <flux:badge color="{{ $post->status === 'published' ? 'green' : 'zinc' }}">
                {{ ucfirst($post->status) }}
            </flux:badge>
            <div class="flex gap-2">
                <flux:button size="sm" variant="outline">Edit</flux:button>
                <flux:button size="sm" variant="ghost" icon="trash" />
            </div>
        </div>
    </flux:card>
</div>
