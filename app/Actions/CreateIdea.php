<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateIdea
{
    public function __construct(#[CurrentUser()] protected User $user) {}

    public function handle(array $attributes): void
    {
        // 1. Filter out the steps for a moment to create the base Idea
        $ideaData = collect($attributes)->only(['title', 'description', 'status', 'links'])->toArray();

        DB::transaction(function () use ($ideaData, $attributes): void {
            // 2. Image handling
            if (($attributes['image'] ?? null) instanceof UploadedFile) {
                $ideaData['image_path'] = $attributes['image']->store('ideas', 'public');
            }

            // 3. Create the parent Idea
            $idea = $this->user->ideas()->create($ideaData);

            // 4. Map the steps. We MUST pull 'description' out of the nested array.
            $steps = collect($attributes['steps'] ?? [])
                ->filter(fn ($step) => ! empty($step['description']))
                ->map(fn ($step) => [
                    'description' => $step['description'],
                    // Cast strictly to a boolean for the model cast to handle
                    'completed' => filter_var($step['completed'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ])
                ->values()
                ->toArray();

            if (! empty($steps)) {
                $idea->steps()->createMany($steps);
            }
        });
    }
}
