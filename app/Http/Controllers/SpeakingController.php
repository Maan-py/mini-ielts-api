<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class SpeakingController extends Controller
{
    public function questions()
    {
        $questions = Question::all();

        return response()->json($questions);
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            "question_id" => "required|exists:questions,id",
            "answer" => "required|string"
        ]);

        return response()->json($data); 
    }
}
