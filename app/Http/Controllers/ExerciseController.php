<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function index()
    {
        $exercises = Exercise::latest()->get();
        return view('trainer.exercises.index', compact('exercises'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'muscle_group' => 'required|string|max:100',
            'description' => 'nullable|string',
            'media_reference' => 'nullable|string',
        ]);

        Exercise::create([
            'name' => $request->name,
            'muscle_group' => $request->muscle_group,
            'description' => $request->description,
            'media_reference' => $request->media_reference,
        ]);

        return redirect()->route('exercises.index')->with('success', 'Exercise added to library successfully!');
    }

    public function destroy(int $id)
{
    $exercise = Exercise::findOrFail($id);
    $exercise->delete();

    return redirect()->route('exercises.index')->with('success', 'Exercise deleted successfully!');
}
}