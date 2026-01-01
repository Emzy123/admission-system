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
            'required_subjects' => json_encode(['mathematics', 'english', 'physics']), // Standardize lowercase
            'catchment_states' => ['Lagos', 'Ogun']
        ]);

        Course::create([
            'name' => 'Medicine',
            'cutoff' => 250,
            'quota' => 1,
            'required_subjects' => json_encode(['mathematics', 'english', 'biology', 'chemistry'])
        ]);

        // Applicants
        Applicant::create([
            'jamb_reg_no' => 'JAMB001',
            'full_name' => 'John Doe',
            'jamb_score' => 260,
            'olevel' => json_encode(['math' => 'A1', 'english' => 'B2', 'physics' => 'B3', 'chemistry' => 'C4']),
            'state_of_origin' => 'Lagos',
            'course_applied' => 'Computer Science',
            'aggregate' => 60.5,
            'status' => 'pending'
        ]);

        Applicant::create([
            'jamb_reg_no' => 'JAMB002',
            'full_name' => 'Jane Smith',
            'jamb_score' => 240,
            'olevel' => json_encode(['math' => 'C4', 'english' => 'C5', 'physics' => 'C6']),
            'state_of_origin' => 'Ogun',
            'course_applied' => 'Computer Science',
            'aggregate' => 55.0,
            'status' => 'pending'
        ]);

        Applicant::create([
            'jamb_reg_no' => 'JAMB003',
            'full_name' => 'Bob Fail',
            'jamb_score' => 190,
            'olevel' => json_encode(['math' => 'F9']),
            'state_of_origin' => 'Kano',
            'course_applied' => 'Computer Science',
            'aggregate' => 40.0,
            'status' => 'pending'
        ]);

        Applicant::create([
            'jamb_reg_no' => 'JAMB004',
            'full_name' => 'Alice Doc',
            'jamb_score' => 280,
            'olevel' => json_encode(['math' => 'A1', 'english' => 'A1', 'biology' => 'A1', 'chemistry' => 'A1']),
            'state_of_origin' => 'Abuja',
            'course_applied' => 'Medicine',
            'aggregate' => 75.0,
            'status' => 'pending'
        ]);
        
        Applicant::create([
            'jamb_reg_no' => 'JAMB005',
            'full_name' => 'Late Comer',
            'jamb_score' => 260,
            'olevel' => json_encode(['math' => 'A1', 'english' => 'A1', 'biology' => 'A1', 'chemistry' => 'A1']),
            'state_of_origin' => 'Abuja',
            'course_applied' => 'Medicine',
            'aggregate' => 70.0,
            'status' => 'pending'
        ]);
        // Admin
        \App\Models\User::factory()->create([
            'name' => 'University Admin',
            'email' => 'emmanuelocheme86@gmail.com',
            'password' => bcrypt('Admin@universityportal'),
        ]);
    }
}
