<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::create([
            "part" => 1,
            "topic" => "Introduction",
            "prompt" => "Can you tell me about yourself?"
        ]);

        Question::create([
            "part" => 2,
            "topic" => "Hometown",
            "prompt" => "Where are you from?"
        ]);

        Question::create([
            "part" => 3,
            "topic" => "Describe a person",
            "prompt" => "Describe a person who has influenced you"
        ]);
    }
}
