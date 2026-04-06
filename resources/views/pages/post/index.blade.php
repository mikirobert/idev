<?php
use Livewire\Volt\Component;
use App\Models\Post;
use Livewire\Attributes\Layout;

new #[Layout('components.layout')] class extends Component {
    public function with()
    {
        $realPosts = Post::latest()->get();

        // Dacă nu ai postări în DB, creăm câteva "fake" pentru design
        if ($realPosts->isEmpty()) {
            $posts = collect(range(1, 6))->map(
                fn($i) => (object) [
                    'id' => $i,
                    'title' => "Postare Exemplu #$i",
                    'content' => 'Acesta este un text generat pentru a testa aspectul cardului Flux. Livewire Volt face dezvoltarea mult mai rapidă.',
                    'status' => $i % 2 == 0 ? 'published' : 'draft',
                    'created_at' => now()->subDays($i),
                ],
            );
        } else {
            $posts = $realPosts;
        }

        return [
            'posts' => $posts,
        ];
    }
}; ?>

<div class="max-w-6xl mx-auto px-6 py-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Posts</flux:heading>
            <flux:subheading>Manage your blog posts and articles</flux:subheading>
        </div>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" icon="plus">New post</flux:button>
        </div>
    </div>

    <flux:separator class="my-8" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($posts as $post)
            <livewire:post.card :$post :key="$post->id" lazy />
        @endforeach
    </div>
</div>
