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
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('original_deadline')->nullable()->after('deadline');
            $table->timestamp('rescheduled_at')->nullable()->after('original_deadline');
            $table->unsignedInteger('reschedule_count')->default(0)->after('rescheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['original_deadline', 'rescheduled_at', 'reschedule_count']);
        });
    }
};
