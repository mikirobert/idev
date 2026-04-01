<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Idea;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateIdea
{
    public function handle(array $attributes, Idea $idea): void
    {
        $data = collect($attributes)->only(['title', 'description', 'status', 'links'])->toArray();

        DB::transaction(function () use ($idea, $data, $attributes): void {
            if (($attributes['image'] ?? null) instanceof UploadedFile) {
                $data['image_path'] = $attributes['image']->store('ideas', 'public');
            }

            $idea->update($data);

            $incomingSteps = collect($attributes['steps'] ?? []);

            // Delete steps that were removed in the UI
            $keepIds = $incomingSteps->pluck('id')->filter()->toArray();
            $idea->steps()->whereNotIn('id', $keepIds)->delete();

            // Update or Create
            foreach ($incomingSteps as $stepData) {
                if (empty($stepData['description'])) {
                    continue;
                }

                $idea->steps()->updateOrCreate(
                    ['id' => $stepData['id'] ?? null],
                    [
                        'description' => $stepData['description'],
                        'completed' => filter_var($stepData['completed'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    ]
                );
            }
        });
    }
}
