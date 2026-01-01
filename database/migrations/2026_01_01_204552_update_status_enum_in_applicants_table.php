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
        // We use raw SQL because modifying Enums in Laravel/Doctrine can be tricky across DBs
        // This command works for MySQL/MariaDB which the user is likely using (XAMPP)
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM('pending', 'admitted', 'rejected', 'waitlisted', 'under_review') DEFAULT 'pending'");
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE applicants MODIFY COLUMN status ENUM('pending', 'admitted', 'rejected') DEFAULT 'pending'");
    }


};
