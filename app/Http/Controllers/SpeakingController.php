<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SpeakingEvaluator;
use App\Models\Attempt;
use App\Models\Question;
use Illuminate\Http\Request;
use Laravel\Ai\Enums\Lab;
use OpenApi\Attributes as OA;

class SpeakingController extends Controller
{
    #[OA\Get(
        path: '/api/speaking/attempts',
        tags: ['Speaking'],
        summary: "List user's speaking attempts",
        description: 'Returns all speaking attempts belonging to the authenticated user, ordered newest first.',
        operationId: 'listAttempts',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of attempts',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Attempt'))
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.')])
            ),
        ]
    )]
    public function index(Request $request)
    {
        $attempts = $request->user()->attempts()->with('question')->latest()->get();

        return response()->json($attempts);
    }

    #[OA\Get(
        path: '/api/speaking/attempts/{id}',
        tags: ['Speaking'],
        summary: 'Get a specific speaking attempt',
        description: 'Returns the detail of a single speaking attempt. Only returns attempts belonging to the authenticated user.',
        operationId: 'showAttempt',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'The attempt ID', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Attempt detail', content: new OA\JsonContent(ref: '#/components/schemas/Attempt')),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.')])
            ),
            new OA\Response(
                response: 404,
                description: 'Attempt not found or does not belong to the user',
                content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string', example: 'No query results for model [App\\Models\\Attempt].')])
            ),
        ]
    )]
    public function show(Request $request, $id)
    {
        $attempt = $request->user()->attempts()->with('question')->findOrFail($id);

        return response()->json($attempt);
    }

    #[OA\Get(
        path: '/api/speaking/questions',
        tags: ['Speaking'],
        summary: 'List all speaking questions',
        description: 'Returns all available IELTS speaking questions. No authentication required.',
        operationId: 'listQuestions',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of questions',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Question'))
            ),
        ]
    )]
    public function questions()
    {
        $questions = Question::all();

        return response()->json($questions);
    }

    #[OA\Post(
        path: '/api/speaking/submit',
        tags: ['Speaking'],
        summary: 'Submit a speaking answer for AI evaluation',
        description: 'Submits a text answer for a given speaking question. The answer is evaluated by an AI (Gemini) which returns an IELTS band score, strengths, and areas to improve.',
        operationId: 'submitAnswer',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['question_id', 'answer'],
                properties: [
                    new OA\Property(property: 'question_id', type: 'integer', example: 1, description: 'ID of the speaking question to answer'),
                    new OA\Property(
                        property: 'answer',
                        type: 'string',
                        example: 'I am from Jakarta. It is a very busy and exciting city. I enjoy the street food and the cultural diversity.'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Evaluation successful',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'attempt', ref: '#/components/schemas/Attempt')]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.')])
            ),
            new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
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
            'attempt' => $attempt->load('question'),
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
