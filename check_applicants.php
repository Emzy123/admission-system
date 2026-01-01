<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = App\Models\Applicant::count();
    echo "Applicant Count (Eloquent): " . $count . "\n";
    
    $all = App\Models\Applicant::all();
    foreach($all as $a) {
        echo " - " . $a->full_name . " (" . $a->email . ")\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
