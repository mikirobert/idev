<?php

namespace Database\Factories;

use App\IdeaStatus;
use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Idea>
 */
class IdeaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(IdeaStatus::cases())->value,
            'links' => [fake()->url(), fake()->url()], // links stays here if it's a JSON column
            'image_path' => null,
        ];
    }

    /**
     * Configure the factory to add steps after creation.
     */
    public function configure()
    {
        return $this->afterCreating(function (Idea $idea) {
            // Create 3 random steps for this idea
            Step::factory()->count(3)->create([
                'idea_id' => $idea->id,
            ]);
        });
    }
}
