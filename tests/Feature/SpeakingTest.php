<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

use App\Ai\Agents\SpeakingEvaluator;
use App\Models\Attempt;
use App\Models\Question;

test("can get speaking questions", function () {
    Question::create([
        "part" => 1,
        "topic" => "Hometown",
        "prompt" => "Where are you from?"
    ]);

    $response = $this->getJson("/api/speaking/questions");

    $response->assertStatus(200)->assertJsonCount(1);
});

test("can submit a speaking answer", function () {
    SpeakingEvaluator::fake([
        [
            "estimated_band" => 5,
            "strengths" => [
                "Clear and understandable answer.",
                "Uses appropriate vocabulary.",
            ],
            "areas_to_improve" => [
                "Provide more details.",
                "Use a wider range of vocabulary.",
            ],
        ]
    ]);

    $user = \App\Models\User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $question = Question::create([
        "part" => 1,
        "topic" => "Hobbies",
        "prompt" => "What do you like to do in your free time?"
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/speaking/submit", [
            "question_id" => $question->id,
            "answer" => "I really enjoy playing football with my friends.",
        ]);

    $response->assertStatus(200);

    $response->assertJsonPath('attempt.question_id', $question->id);
    $response->assertJsonPath('attempt.estimated_band', 5);

    expect(Attempt::count())->toBe(1);
    expect(Attempt::first()->user_id)->toBe($user->id);
});

test('speaking evaluator fake works', function () {
    SpeakingEvaluator::fake([
        [
            'estimated_band' => 5,
            'strengths' => [
                'Test strength',
            ],
            'areas_to_improve' => [
                'Test improvement',
            ],
        ],
    ]);

    // SpeakingEvaluator::preventStrayPrompts();

    $agent = new SpeakingEvaluator();

    $response = $agent->prompt(
        'Test IELTS answer'
    );

    expect($response['estimated_band'])->toBe(5);
});

test("cannot submit invalid speaking answer", function () {
    $user = \App\Models\User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/speaking/submit", [
            "question_id" => 999999,
            "answer" => "",
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            "question_id",
            "answer",
        ]);
});

test("attempt is linked to authenticated user via sanctum token", function () {
    SpeakingEvaluator::fake([
        [
            "estimated_band" => 6,
            "strengths" => ["Good fluency."],
            "areas_to_improve" => ["Add more vocabulary."],
        ]
    ]);

    $user = \App\Models\User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $question = Question::create([
        "part" => 1,
        "topic" => "Education",
        "prompt" => "Describe your education.",
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/speaking/submit", [
            "question_id" => $question->id,
            "answer" => "I studied at a university.",
        ]);

    $response->assertStatus(200);

    $attempt = Attempt::first();

    expect($attempt->user_id)->toBe($user->id);
    expect($attempt->user->id)->toBe($user->id);
});

test("submit without token is rejected", function () {
    $question = Question::create([
        "part" => 1,
        "topic" => "Hobbies",
        "prompt" => "What do you like?",
    ]);

    $response = $this->postJson("/api/speaking/submit", [
        "question_id" => $question->id,
        "answer" => "I like reading.",
    ]);

    $response->assertStatus(401);
    expect(Attempt::count())->toBe(0);
});

test("user can only view their own attempts list", function () {
    $user1 = \App\Models\User::factory()->create();
    $user2 = \App\Models\User::factory()->create();
    $token1 = $user1->createToken('test')->plainTextToken;

    $question = Question::create([
        "part" => 1,
        "topic" => "Hobbies",
        "prompt" => "What do you like?",
    ]);

    Attempt::create([
        "user_id" => $user1->id,
        "question_id" => $question->id,
        "answer_text" => "User 1 answer",
    ]);

    Attempt::create([
        "user_id" => $user2->id,
        "question_id" => $question->id,
        "answer_text" => "User 2 answer",
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
        ->getJson("/api/speaking/attempts");

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonPath('0.user_id', $user1->id);
});

test("user cannot view another user attempt detail", function () {
    $user1 = \App\Models\User::factory()->create();
    $user2 = \App\Models\User::factory()->create();
    $token1 = $user1->createToken('test')->plainTextToken;

    $question = Question::create([
        "part" => 1,
        "topic" => "Hobbies",
        "prompt" => "What do you like?",
    ]);

    $attemptUser2 = Attempt::create([
        "user_id" => $user2->id,
        "question_id" => $question->id,
        "answer_text" => "User 2 answer",
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
        ->getJson("/api/speaking/attempts/" . $attemptUser2->id);

    $response->assertStatus(404);
});
