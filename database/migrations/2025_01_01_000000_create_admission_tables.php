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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->string('gender')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('jamb_reg_no')->unique()->nullable();
            $table->integer('jamb_score')->nullable();
            $table->json('jamb_details')->nullable();
            $table->json('olevel')->nullable();
            $table->string('course_applied')->nullable();
            $table->decimal('aggregate', 5, 2)->nullable();
            $table->enum('status', ['pending', 'admitted', 'rejected', 'waitlisted', 'under_review'])->default('pending');
            $table->text('reason')->nullable(); // For detailed feedback
            $table->boolean('is_submitted')->default(false);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('cutoff');
            $table->integer('quota');
            $table->json('required_subjects');
            $table->json('catchment_states')->nullable();
            $table->timestamps();
        });

        Schema::create('admission_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->enum('decision', ['admitted', 'rejected']);
            $table->enum('jamb_status', ['pending', 'confirmed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_results');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('applicants');
    }
};
