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
            'aggregate' => $request->jamb_score / 4 // Simplified aggregate
        ]);

        return back()->with('success', 'Applicant added successfully.');
    });

    Route::post('/admin/applicants/csv', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        fgetcsv($handle); // Skip header

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
             // Expecting: RegNo, Name, Email, Score, Course, State
             if(count($row) < 6) continue;

             Applicant::updateOrCreate(
                ['email' => $row[2]], 
                [
                    'jamb_reg_no' => $row[0],
                    'full_name' => $row[1],
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'jamb_score' => $row[3],
                    'course_applied' => $row[4],
                    'state_of_origin' => $row[5],
                    'status' => 'pending',
                    'is_submitted' => true,
                    'aggregate' => $row[3] / 4
                ]
             );
        }
        fclose($handle);

        return back()->with('success', 'Bulk upload processed successfully.');
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
    Route::post('/admission/run', function () {
        $pending = Applicant::where('status', 'pending')->where('is_submitted', true)->get();
        $courses = Course::all()->keyBy('name');

        $processed = 0;
        foreach($pending as $applicant) {
            $course = $courses->get($applicant->course_applied);
            
            // Default logic: Admit if JAMB score >= Course Cutoff
            // Fallback: If course not found in DB, assume cutoff 180
            $cutoff = $course ? $course->cutoff : 180; 

            $status = 'rejected';
            $message = "We regret to inform you that you have not been offered admission due to not meeting the cutoff criteria.";

            if ($applicant->jamb_score >= $cutoff) {
                $status = 'admitted';
                $message = "Congratulations! You have been offered provisional admission into " . $applicant->course_applied;
            }
            
            $applicant->update(['status' => $status]);
            $applicant->notify(new \App\Notifications\AdmissionDecision($status, $message));

            $processed++;
        }
        
        return redirect('/admission/results')->with('success', "Admission process completed. $processed applicants evaluated.");
    });

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
