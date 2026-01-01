<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Applicant;
use Illuminate\Support\Facades\Hash;

try {
    Applicant::create([
        'jamb_reg_no' => 'JAMB003',
        'full_name' => 'Low Scorer',
        'email' => 'low@example.com',
        'password' => Hash::make('password'),
        'jamb_score' => 150,
        'olevel' => ['math' => 'C6'],
        'state_of_origin' => 'Lagos',
        'course_applied' => 'Computer Science', 
        'aggregate' => 40.0,
        'status' => 'pending',
        'is_submitted' => true
    ]);
    
    echo "Rejected Applicant Created.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
