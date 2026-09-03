<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId("question_id")->constrained()->onDelete("cascade");

            $table->text("answer_text");
            $table->decimal("estimated_band", 2, 1)->nullable();
            $table->text("strengths")->nullable();
            $table->text("areas_to_improve")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
