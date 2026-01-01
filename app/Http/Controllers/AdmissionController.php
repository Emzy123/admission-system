<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Applicant;
use App\Models\AdmissionResult;
use App\Services\RuleEngine;
use App\Services\RankingEngine;
use App\Services\QuotaEngine;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        $results = AdmissionResult::with(['applicant', 'course'])->latest()->paginate(20);
        return view('admission.index', compact('courses', 'results'));
    }

    public function generate($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        // Ensure no previous results for this course to avoid duplicates if re-running (optional logic)
        // AdmissionResult::where('course_id', $course->id)->delete(); 

        $applicants = Applicant::where('course_applied', $course->name)
                               ->where('status', 'pending') // Only process pending
                               ->get();

        $rule = new RuleEngine();
        $ranking = new RankingEngine();
        $quota = new QuotaEngine();

        // 1. Qualify
        $qualified = $applicants->filter(fn($a) => $rule->qualifies($a, $course));

        // 2. Rank
        $ranked = $ranking->rank($qualified);

        // 3. Apply Quota
        $admitted = $quota->apply($ranked, $course->quota);

        foreach ($admitted as $applicant) {
            AdmissionResult::create([
                'applicant_id' => $applicant->id,
                'course_id' => $course->id,
                'decision' => 'admitted',
                'jamb_status' => 'pending'
            ]);

            $applicant->update(['status' => 'admitted']);
        }

        return back()->with('success', 'Admission list generated successfully. ' . $admitted->count() . ' candidates admitted.');
    }
}
