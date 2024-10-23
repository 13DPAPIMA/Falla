<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Display a listing of the answers.
     */
    public function index()
    {
        $answers = Answer::with('question')->get();
        return response()->json($answers);
    }

    /**
     * Store a newly created answer in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
            'question_id' => 'required|exists:questions,id',
        ]);

        $answer = Answer::create([
            'user_id' => $request->user()->id, // Assumes the user is authenticated
            'question_id' => $validatedData['question_id'],
            'content' => $validatedData['content'],
        ]);

        return response()->json($answer, 201);
    }

    /**
     * Display the specified answer.
     */
    public function show(Answer $answer)
    {
        return response()->json($answer->load('question'));
    }

    /**
     * Update the specified answer in storage.
     */
    public function update(Request $request, Answer $answer)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);

        $answer->update($validatedData);

        return response()->json($answer);
    }

    /**
     * Remove the specified answer from storage.
     */
    public function destroy(Answer $answer)
    {
        $answer->delete();

        return response()->json(null, 204);
    }
}
