<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Applicant;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Courses
        Course::create([
            'name' => 'Computer Science',
            'cutoff' => 200,
            'quota' => 2,
            'required_subjects' => json_encode(['mathematics', 'english', 'physics']),
            'catchment_states' => ['Lagos', 'Ogun']
        ]);

        Course::create([
            'name' => 'Medicine',
            'cutoff' => 250,
            'quota' => 1,
            'required_subjects' => json_encode(['mathematics', 'english', 'biology', 'chemistry'])
        ]);

        // Applicants

        // 1. John Doe: Good student, Upward Trend
        Applicant::create([
            'jamb_reg_no' => 'JAMB001',
            'email' => 'john@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'full_name' => 'John Doe',
            'jamb_score' => 260,
            'olevel' => json_encode(['mathematics' => 'A1', 'english' => 'B2', 'physics' => 'B3', 'chemistry' => 'C4']),
            'state_of_origin' => 'Lagos',
            'course_applied' => 'Computer Science',
            'aggregate' => 60.5,
            'status' => 'pending',
            'academic_trend' => 'upward', // Bonus
            'recommendation_score' => 7,
            'is_submitted' => true
        ]);

        // 2. Jane Smith: Average student, Stable
        Applicant::create([
            'jamb_reg_no' => 'JAMB002',
            'email' => 'jane@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'full_name' => 'Jane Smith',
            'jamb_score' => 240,
            'olevel' => json_encode(['mathematics' => 'C4', 'english' => 'C5', 'physics' => 'C6']),
            'state_of_origin' => 'Ogun',
            'course_applied' => 'Computer Science',
            'aggregate' => 55.0,
            'status' => 'pending',
            'academic_trend' => 'stable',
            'is_submitted' => true
        ]);

        // 3. Bob Fail: Disciplinary Issue (Should be flagged)
        Applicant::create([
            'jamb_reg_no' => 'JAMB003',
            'email' => 'bob@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'full_name' => 'Bob Fail',
            'jamb_score' => 190,
            'olevel' => json_encode(['mathematics' => 'F9']),
            'state_of_origin' => 'Kano',
            'course_applied' => 'Computer Science',
            'aggregate' => 40.0,
            'status' => 'pending',
            'has_disciplinary_record' => true, // Red Flag
            'is_submitted' => true
        ]);

        // 4. Alice Doc: Excellent, High Recommendation
        Applicant::create([
            'jamb_reg_no' => 'JAMB004',
            'email' => 'alice@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'full_name' => 'Alice Doc',
            'jamb_score' => 280,
            'olevel' => json_encode(['mathematics' => 'A1', 'english' => 'A1', 'biology' => 'A1', 'chemistry' => 'A1']),
            'state_of_origin' => 'Abuja',
            'course_applied' => 'Medicine',
            'aggregate' => 75.0,
            'status' => 'pending',
            'recommendation_score' => 9, // Bonus
            'is_submitted' => true
        ]);
        
        // 5. Late Comer: Good but duplicates Medicine (Quota Test)
        Applicant::create([
            'jamb_reg_no' => 'JAMB005',
            'email' => 'late@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'full_name' => 'Late Comer',
            'jamb_score' => 260,
            'olevel' => json_encode(['mathematics' => 'A1', 'english' => 'A1', 'biology' => 'A1', 'chemistry' => 'A1']),
            'state_of_origin' => 'Abuja',
            'course_applied' => 'Medicine',
            'aggregate' => 70.0,
            'status' => 'pending',
            'is_submitted' => true
        ]);

        // Admin
        \App\Models\User::create([
            'name' => 'University Admin',
            'email' => 'emmanuelocheme86@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Admin@universityportal'),
        ]);
    }
}
