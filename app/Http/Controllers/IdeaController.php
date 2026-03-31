<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\IdeaStatus;
use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'status' => ['nullable', 'in:'.implode(',', array_column(IdeaStatus::cases(), 'value'))],
        ]);

        $ideas = $user->ideas()
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->get();

        return view('idea.index', [
            'ideas' => $ideas,
            'statusCounts' => Idea::statusCounts($user),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIdeaRequest $request)
    {
        // dd($request->all());

        $idea = Auth::user()->ideas()->create($request->safe()->except(['steps', 'image']));

        $idea->steps()->createMany(
            collect($request->steps)->map(fn ($step) => ['description' => $step])
        );
        if ($request->hasFile('image')) {
            $imagePath = $request->image->store('ideas', 'public');

            $idea->update([
                'image_path' => $imagePath,
            ]);
        }

        return to_route('idea.index')->with('succes', 'Idea created!');

    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea)
    {
        return view('idea.show', [
            'idea' => $idea,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIdeaRequest $request, Idea $idea): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea)
    {
        // authorize that this is alowed
        $idea->delete();

        return to_route('idea.index');
    }
}
