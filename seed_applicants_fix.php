<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Applicant;
use Illuminate\Support\Facades\Hash;

try {
    Applicant::create([
        'jamb_reg_no' => 'JAMB001',
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password'),
        'jamb_score' => 260,
        'olevel' => ['math' => 'A1', 'english' => 'B2', 'physics' => 'B3', 'chemistry' => 'C4'],
        'state_of_origin' => 'Lagos',
        'course_applied' => 'Computer Science',
        'aggregate' => 60.5,
        'status' => 'pending',
        'is_submitted' => true
    ]);

    echo "Applicant 1 Created.\n";

    Applicant::create([
        'jamb_reg_no' => 'JAMB002',
        'full_name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'password' => Hash::make('password'),
        'jamb_score' => 240,
        'olevel' => ['math' => 'C4', 'english' => 'C5', 'physics' => 'C6'],
        'state_of_origin' => 'Ogun',
        'course_applied' => 'Computer Science', // Make sure this matches a real course
        'aggregate' => 55.0,
        'status' => 'pending',
        'is_submitted' => true
    ]);
    
    echo "Applicant 2 Created.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
