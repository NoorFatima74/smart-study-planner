<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('ai_study_plans', function (Blueprint $table) {
        $table->unsignedBigInteger('subject_id')->nullable()->change();
        $table->date('exam_date')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('ai_study_plans', function (Blueprint $table) {
        $table->unsignedBigInteger('subject_id')->nullable(false)->change();
        $table->date('exam_date')->nullable(false)->change();
    });
}
};
