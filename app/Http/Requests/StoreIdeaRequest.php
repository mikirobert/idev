<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\IdeaStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreIdeaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required', 'string', 'max:255',
                Rule::unique('ideas')->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'description' => ['nullable', 'string', 'max:4000'],
            'status' => ['required', Rule::enum(IdeaStatus::class)],
            'links' => ['nullable', 'array'],
            'links.*' => ['url', 'max:255'],
            'steps' => ['nullable', 'array'],
            'steps.*.description' => ['nullable', 'string', 'max:500'],
            'steps.*.completed' => ['nullable', 'in:0,1,true,false'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
