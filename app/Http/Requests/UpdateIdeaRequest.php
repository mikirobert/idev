<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\IdeaStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateIdeaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        // 1. DYNAMIC ID FETCHING
        // This checks 'idea' (Model Binding) AND 'id' (Direct Parameter)
        $idea = $this->route('idea');

        // Extract the integer ID regardless of what the route returns
        $ideaId = is_object($idea) ? $idea->id : $idea;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ideas', 'title')
                    ->ignore($ideaId) // This is the magic line
                    ->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'description' => ['nullable', 'string', 'max:4000'],
            'status' => ['required', Rule::enum(IdeaStatus::class)],
            'links' => ['nullable', 'array'],
            'links.*' => ['url', 'max:255'],
            'steps' => ['nullable', 'array'],
            'steps.*.id' => ['nullable'],
            'steps.*.description' => ['nullable', 'string', 'max:500'],
            'steps.*.completed' => ['nullable'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
