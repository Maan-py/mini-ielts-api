<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SpeakingEvaluator;
use App\Models\Attempt;
use App\Models\Question;
use Illuminate\Http\Request;
use Laravel\Ai\Enums\Lab;

class SpeakingController extends Controller
{
    public function index(Request $request)
    {
        $attempts = $request->user()->attempts()->with('question')->latest()->get();

        return response()->json($attempts);
    }

    public function show(Request $request, $id)
    {
        $attempt = $request->user()->attempts()->with('question')->findOrFail($id);

        return response()->json($attempt);
    }

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

        $question = Question::findOrFail($data["question_id"]);

        $agent = new SpeakingEvaluator();

        $evaluation = $agent->prompt(
            <<<PROMPT
            Evaluate this IELTS speaking answer.

            Question:
            {$question->prompt}

            Student Answer:
            {$data["answer"]}
            PROMPT,
            provider: Lab::Gemini,
            model: "gemini-3.6-flash",
            timeout: 120

        );


        $attempt = Attempt::create([
            "user_id" => $request->user()?->id,
            "question_id" => $data["question_id"],
            "answer_text" => $data["answer"],
            "estimated_band" => $evaluation["estimated_band"],
            "strengths" => $evaluation["strengths"],
            "areas_to_improve" => $evaluation["areas_to_improve"],
        ]);

        return response()->json([
            'attempt' => $attempt,
        ]);
    }

    public function testGemini()
    {
        $agent = new SpeakingEvaluator();

        $response = $agent->prompt(
            "Evaluate this IELTS speaking answer: I really enjoy playing football with my friends.",
            provider: Lab::Gemini,
            model: "gemini-3.6-flash",
            timeout: 120,
        );

        return response()->json($response);
    }
}
