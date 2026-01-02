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
        Schema::create('admitted_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('jamb_reg_no')->unique(); // Ensures no duplicates
            $table->string('full_name');
            $table->string('course_admitted');
            $table->string('gender')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->integer('jamb_score')->nullable();
            $table->string('status')->default('admitted'); // Always admitted in this table
            $table->timestamp('admitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admitted_candidates');
    }
};
