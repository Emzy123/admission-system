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
        Schema::table('applicants', function (Blueprint $table) {
            $table->boolean('has_disciplinary_record')->default(false)->after('status');
            $table->string('academic_trend')->default('stable')->after('has_disciplinary_record'); // upward, stable, downward
            $table->integer('recommendation_score')->default(5)->after('academic_trend'); // 0-10
            $table->integer('hardship_bonus')->default(0)->after('recommendation_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            //
        });
    }
};
