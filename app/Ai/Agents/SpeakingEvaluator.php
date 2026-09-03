<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\ProviderTool;
use Stringable;

class SpeakingEvaluator implements Agent, Conversational, HasTools, HasStructuredOutput
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<PROMPT
        You are an IELTS Speaking evaluator.

        Evaluate the student's answer based on the IELTS Speaking criteria that can
        reasonably be assessed from text:

        - Fluency and Coherence
        - Lexical Resource
        - Grammatical Range and Accuracy

        Do not evaluate pronunciation because the student's answer is provided as text only.

        Evaluate the answer in relation to the specific question given.
        Do not give feedback about information that is unrelated to the question.

        Estimate an IELTS Speaking band score from 0 to 9.
        The estimated band must use increments of 0.5 only.

        Provide constructive and specific feedback.

        The evaluation must contain:
        - estimated_band
        - strengths
        - areas_to_improve
        PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'estimated_band' => $schema
                ->number()
                ->min(0)
                ->max(9)
                ->required(),

            'strengths' => $schema->array()
                ->items(
                    $schema->string()
                )
                ->required(),

            'areas_to_improve' => $schema->array()
                ->items(
                    $schema->string()
                )
                ->required(),
        ];
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return list<Agent|Tool|ProviderTool>
     */
    public function tools(): iterable
    {
        return [];
    }
}
