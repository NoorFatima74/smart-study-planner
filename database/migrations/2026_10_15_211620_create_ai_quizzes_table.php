<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('ai_quizzes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('subject');
        $table->string('topic')->nullable();
        $table->string('level');
        $table->json('questions');
        $table->json('answers')->nullable();
        $table->unsignedTinyInteger('score')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_quizzes');
    }
};
