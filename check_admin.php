<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'emmanuelocheme86@gmail.com')->first();
if ($user) {
    echo "Found Admin: " . $user->name . " | " . $user->email . "\n";
} else {
    echo "Admin User NOT FOUND.\n";
}

$count = App\Models\User::count();
echo "Total Users in DB: " . $count . "\n";

echo "DB Connection: " . DB::connection()->getDatabaseName() . "\n";
