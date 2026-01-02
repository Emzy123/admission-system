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
            'waitlisted' => Applicant::where('status', 'waitlisted')->count(),
            'under_review' => Applicant::where('status', 'under_review')->count(),
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
        $errors = [];

        // Skip Header explicitly if not empty
        // fgetcsv($handle); // Optional: we can check row content inside loop

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
             $countProcessed++;
             
             // 1. Basic Validation: Skip empty rows or Header
             if (empty($row[0])) continue;
             if ($row[0] === 'Registration Number') continue; // Skip Header row if found

             // Map Columns (Official JAMB 29 Cols)
             // 0:RegNo, 1:Name, 2:Gender, 3:State, 4:LGA, 5:UTME Score
             // 6:Sub1, 7:Scr1, 8:Sub2, 9:Scr2, 10:Sub3, 11:Scr3, 12:Sub4, 13:Scr4
             // 14:Course, 15:Inst, 16:Status, 17:Email, 18:Phone
             // 19:O-Sub1, 20:O-Grd1 ... 27:O-Sub5, 28:O-Grd5

             // Build JAMB Details
             $jambDetails = [];
             for ($i = 6; $i <= 12; $i += 2) {
                 if (!empty($row[$i])) {
                     $jambDetails[] = [
                         'subject' => $row[$i],
                         'score' => intval($row[$i+1] ?? 0)
                     ];
                 }
             }

             // Build O-Level Array
             $olevel = [];
             for ($i = 19; $i <= 27; $i += 2) {
                 if (!empty($row[$i]) && !empty($row[$i+1])) {
                     $olevel[strtolower(trim($row[$i]))] = strtoupper(trim($row[$i+1]));
                 }
             }
            
             // Determine Email (Fallback if missing)
             $email = !empty($row[17]) ? $row[17] : strtolower(str_replace(' ', '.', $row[1])) . '_' . substr($row[0], -4) . '@example.com';

             try {
                 // Create/Update Applicant
                 Applicant::updateOrCreate(
                    ['jamb_reg_no' => $row[0]], 
                    [
                        'full_name' => $row[1],
                        'gender' => $row[2] ?? null,
                        'state_of_origin' => $row[3],
                        'lga' => $row[4] ?? null,
                        'jamb_score' => intval($row[5]), // Score is Col 5
                        'jamb_details' => $jambDetails,
                        'course_applied' => $row[14],
                        'email' => $email,
                        'phone_number' => $row[18] ?? null,
                        'olevel' => $olevel,
                        
                        'status' => 'pending',
                        'is_submitted' => true,
                        'password' => \Illuminate\Support\Facades\Hash::make('password'),
                        'aggregate' => 0
                    ]
                 );
                 $countInserted++;
             } catch (\Exception $e) {
                 $countSkipped++;
                 // Store first 5 errors to display to user
                 if (count($errors) < 5) {
                     $errors[] = "Row " . $countProcessed . ": " . $e->getMessage();
                 }
                 continue; // Skip this row and move to next
             }
        }
        fclose($handle);

        $message = "Upload Processed. Inserted: $countInserted, Failed: $countSkipped.";
        if (!empty($errors)) {
            $message .= " First Error: " . $errors[0];
        }

        return response()->json([
            'message' => $message,
            'inserted' => $countInserted,
            'skipped' => $countSkipped,
            'errors' => $errors
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
    Route::post('/admission/manual/admit/{id}', [AdmissionController::class, 'manualAdmit']);
    Route::post('/admission/manual/reject/{id}', [AdmissionController::class, 'manualReject']);

    // Admission Results (Admin)
    Route::get('/admission/results', function () {
        $admitted = Applicant::where('status', 'admitted')->get();
        $rejected = Applicant::where('status', 'rejected')->get();
        $reviews = Applicant::whereIn('status', ['waitlisted', 'under_review'])->get();
        
        return view('admission.results', compact('admitted', 'rejected', 'reviews'));
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
    }
});

Route::get('/system/debug-schema', function () {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('applicants');
    $migrations = \Illuminate\Support\Facades\DB::table('migrations')->get();
    return response()->json([
        'columns' => $columns,
        'migrations' => $migrations
    ]);
});
