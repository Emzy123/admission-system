<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\PortalController;
use App\Models\Applicant;
use App\Models\Course;
use App\Models\AdmissionResult;

Route::get('/', function () {
    return redirect('/login');
});

// --- ADMIN ROUTES ---
Route::prefix('admin')->middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [App\Http\Controllers\AdminAuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [App\Http\Controllers\AdminAuthController::class, 'logout'])->name('admin.logout'); // Naming it logic

    Route::get('/dashboard', function () {
        $metrics = [
            'total_applicants' => Applicant::count(),
            'eligible' => Applicant::where('status', 'pending')->where('is_submitted', true)->count(),
            'admitted' => Applicant::where('status', 'admitted')->count(),
            'rejected' => Applicant::where('status', 'rejected')->count()
        ];
        return view('dashboard', compact('metrics'));
    });

    // Upload Applicants (Admin)
    Route::get('/applicants/upload', function () {
        return view('applicants.upload');
    });

    Route::post('/admin/applicants/manual', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'jamb_reg_no' => 'required|unique:applicants',
            'full_name' => 'required',
            'email' => 'required|email|unique:applicants',
            'jamb_score' => 'required|numeric',
            'course_applied' => 'required',
            'state' => 'required'
        ]);

        // Process O-Level Array
        $olevel = [];
        if ($request->has('olevel')) {
            foreach ($request->olevel as $item) {
                if (!empty($item['subject']) && !empty($item['grade'])) {
                    $olevel[$item['subject']] = $item['grade'];
                }
            }
        }

        Applicant::create([
            'jamb_reg_no' => $request->jamb_reg_no,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'jamb_score' => $request->jamb_score,
            'course_applied' => $request->course_applied,
            'state_of_origin' => $request->state,
            'status' => 'pending',
            'is_submitted' => true,
            'olevel' => $olevel, // Store as JSON (casted in model)
            'aggregate' => 0 // Will be calculated by Admission Run
        ]);

        return back()->with('success', 'Applicant added with O-Level results.');
    });

    Route::post('/admin/applicants/csv', function (\Illuminate\Http\Request $request) {
        set_time_limit(300); // Increase timeout to 5 minutes

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        fgetcsv($handle); // Skip header

        $countProcessed = 0;
        $countSkipped = 0;
        $countInserted = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
             $countProcessed++;
             // Log first 3 rows to debug structure
             if ($countProcessed <= 3) {
                 \Illuminate\Support\Facades\Log::info("Row {$countProcessed}: " . json_encode($row));
             }

             // Expecting: RegNo, Name, Email, Score, Course, State, Sub1, Gr1, Sub2, Gr2, Sub3, Gr3, Sub4, Gr4, Sub5, Gr5
             // Minimum 6 columns required, but 16 recommended for full O-Level
             if(count($row) < 6) {
                 \Illuminate\Support\Facades\Log::warning("Skipped Row {$countProcessed}: Not enough columns (" . count($row) . ")");
                 $countSkipped++;
                 continue;
             }

             $score = is_numeric($row[3]) ? intval($row[3]) : 0;
             if ($score === 0) {
                 \Illuminate\Support\Facades\Log::warning("Skipped Row {$countProcessed}: Invalid Score '{$row[3]}'");
                 $countSkipped++;
                 continue;
             }

             // Parse O-Levels from columns 6-15 if they exist
             $olevel = [];
             // Pairs start at index 6: 6=>Sub1, 7=>Gr1, 8=>Sub2, 9=>Gr2 ... 14=>Sub5, 15=>Gr5
             for ($i = 6; $i < 16; $i += 2) {
                 if (isset($row[$i]) && isset($row[$i+1])) {
                     $sub = trim($row[$i]);
                     $gr = trim($row[$i+1]);
                     if (!empty($sub) && !empty($gr)) {
                         $olevel[$sub] = $gr;
                     }
                 }
             }

             Applicant::updateOrCreate(
                ['jamb_reg_no' => $row[0]], // Lookup by Unique RegNo instead of Email
                [
                    'email' => $row[2], 
                    'full_name' => $row[1],
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'jamb_score' => $score,
                    'course_applied' => $row[4],
                    'state_of_origin' => $row[5],
                    'status' => 'pending',
                    'is_submitted' => true,
                    'olevel' => $olevel,
                    'aggregate' => 0 // Calculated later
                ]
             );
             $countInserted++;
        }
        fclose($handle);

        \Illuminate\Support\Facades\Log::info("CSV Processing Complete. Processed: $countProcessed, Inserted: $countInserted, Skipped: $countSkipped");
        
        return response()->json([
            'message' => "Bulk upload processed successfully.",
            'inserted' => $countInserted,
            'skipped' => $countSkipped
        ], 200);
    });

    // Admission Rules (Admin)
    Route::get('/admission/rules', function () {
        return view('admission.rules');
    });

    // Process Admission (Admin)
    Route::get('/admission/process', function () {
        $pending = Applicant::where('status', 'pending')->where('is_submitted', true)->get();
        return view('admission.process', compact('pending'));
    });
    Route::post('/admission/run', [AdmissionController::class, 'runAdmission']);

    // Admission Results (Admin)
    Route::get('/admission/results', function () {
        $admitted = Applicant::where('status', 'admitted')->get();
        $rejected = Applicant::where('status', 'rejected')->get();
        return view('admission.results', compact('admitted', 'rejected'));
    });

    // JAMB Confirmation (Admin)
    Route::get('/jamb/confirmation', function () {
        return view('jamb.confirmation');
    });

    // Reports (Admin)
    Route::get('/reports', function () {
        $admittedCount = Applicant::where('status', 'admitted')->count();
        $rejectedCount = Applicant::where('status', 'rejected')->count();

        // Intake by Department
        $intakeByDept = Applicant::where('status', 'admitted')
            ->select('course_applied', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('course_applied')
            ->get();
        
        $deptLabels = $intakeByDept->pluck('course_applied');
        $deptData = $intakeByDept->pluck('total');

        return view('reports.index', compact('admittedCount', 'rejectedCount', 'deptLabels', 'deptData'));
    });
    
    Route::get('/applicants', function () {
        return view('applicants.upload');
    });
});


// --- APPLICANT PORTAL ROUTES ---

// Public/Guest Applicant Routes
Route::middleware('guest:applicant')->group(function () {
    Route::get('/login', [PortalController::class, 'showLogin'])->name('login'); // Standard login name for redirect
    Route::post('/login', [PortalController::class, 'login']);
    Route::get('/register', [PortalController::class, 'showRegister']);
    Route::post('/register', [PortalController::class, 'register']);
});

// Authenticated Applicant Routes
Route::middleware('auth:applicant')->group(function () {
    Route::get('/portal/dashboard', [PortalController::class, 'dashboard'])->name('portal.dashboard');
    Route::get('/portal/apply', [PortalController::class, 'showApplyForm'])->name('portal.apply');
    Route::post('/portal/apply', [PortalController::class, 'submitApplication']);
    Route::post('/portal/logout', [PortalController::class, 'logout'])->name('portal.logout');
});

// --- SYSTEM SETUP ROUTE (REMOVE AFTER USE) ---
Route::get('/system/setup-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh --seed --force');
        return "Database setup completed successfully! Tables created and Admin seeded. <a href='/admin/login'>Go to Admin Login</a>";
    } catch (\Exception $e) {
        return "Setup Failed: " . $e->getMessage();
    }
});

Route::get('/system/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return "Cache cleared successfully! <a href='/portal/apply'>Try Apply URL</a>";
});

Route::get('/system/fix-admin', function () {
    try {
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'emmanuelocheme86@gmail.com'],
            [
                'name' => 'University Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@universityportal') // Force hash
            ]
        );
        return "Admin User Fixed!<br>Email: emmanuelocheme86@gmail.com<br>Password: Admin@universityportal<br><br><a href='/admin/login'>Login Now</a>";
    } catch (\Exception $e) {
        return "Error fixing admin: " . $e->getMessage();
    }
});
