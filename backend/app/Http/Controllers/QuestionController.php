<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // Create
    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required|in:Suggestions,Clothes,Other',
            'description' => 'required|string',
        ]);

        $question = Question::create($request->all());
        return response()->json($question, 201);
    }

    // Read (all)
    public function index()
    {
        $questions = Question::all();
        return response()->json($questions);
    }

    // Read (single)
    public function show($id)
    {
        $question = Question::findOrFail($id);
        return response()->json($question);
    }

    // Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'topic' => 'sometimes|required|in:Suggestions,Clothes,Other',
            'description' => 'sometimes|required|string',
        ]);

        $question = Question::findOrFail($id);
        $question->update($request->all());
        return response()->json($question);
    }

    // Delete
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        return response()->json(null, 204);
    }
}
