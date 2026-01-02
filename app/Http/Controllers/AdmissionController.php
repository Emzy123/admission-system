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
    public function runAdmission()
    {
        $pending = Applicant::where('status', 'pending')->where('is_submitted', true)->get();
        $courses = Course::all()->keyBy('name');
        
        $processed = 0;
        foreach($pending as $applicant) {
            $course = $courses->get($applicant->course_applied);
            $cutoff = $course ? $course->cutoff : 180;

            // 1. Calculate Standardized Aggregate
            // JAMB (60%): (Score / 400) * 60
            $jambPoints = ($applicant->jamb_score / 400) * 60;

            // O-Level (40%): A1=6, B2=5, B3=4, C4=3, C5=2, C6=1. Max 30 points (5 subjects).
            // Calculation: (Points / 30) * 40
            $oLevelPoints = $this->calculateOLevelPoints($applicant->olevel);
            $weightedOLevel = ($oLevelPoints / 30) * 40;

            $finalAggregate = round($jambPoints + $weightedOLevel, 2);

            // 2b. Holistic Scoring (New)
            $holisticBonus = 0;
            
            // Academic Trend
            if ($applicant->academic_trend === 'upward') $holisticBonus += 2;
            if ($applicant->academic_trend === 'downward') $holisticBonus -= 2;

            // Recommendation & Hardship
            $holisticBonus += ($applicant->recommendation_score > 7 ? 2 : 0);
            $holisticBonus += ($applicant->hardship_bonus > 0 ? 1 : 0);

            // Catchment Bonus (Institutional Need)
            if ($course && in_array($applicant->state_of_origin, $course->catchment_states ?? [])) {
                $holisticBonus += 5; 
            }

            // Final Adjusted Aggregate
            $finalScore = $finalAggregate + $holisticBonus;
            $applicant->update(['aggregate' => $finalScore]);

            // 3. Admission Decision with Waitlist Logic
            $status = 'rejected';
            $message = "Admission Declined. Adjusted Score: " . number_format($finalScore, 1);
            $reason = "Score ($finalScore) below cutoff ($cutoff)"; // Default reason
            $missingReq = []; // Initialize array

            // 2c. Check Required Subjects (Subject Verification)
            if ($course && $course->required_subjects) {
                $requirements = is_string($course->required_subjects) ? json_decode($course->required_subjects, true) : $course->required_subjects;
                
                // Normalizing applicant subjects to lowercase for comparison
                $applicantSubjects = [];
                if (is_array($applicant->olevel)) {
                    foreach ($applicant->olevel as $k => $v) {
                         $subName = is_array($v) ? ($v['subject'] ?? '') : $k;
                         $subGrade = is_array($v) ? ($v['grade'] ?? '') : $v;
                         if ($subName) $applicantSubjects[strtolower(trim($subName))] = strtoupper(trim($subGrade));
                    }
                }

                foreach ($requirements as $req) {
                    $req = strtolower(trim($req));
                    $grade = $applicantSubjects[$req] ?? 'F9';
                    $points = $this->getGradePoints($grade);
                    if ($points < 1) { // Strict check: Must have at least C6
                        $missingReq[] = ucfirst($req);
                    }
                }
            }

            // Check Discipline FIRST (Safety)
            if ($applicant->has_disciplinary_record) {
                $status = 'under_review'; 
                $message = "Application flagged for Disciplinary Review. Action Required.";
                $reason = "Flagged: Disciplinary Record Detected.";
            } 
            elseif (empty($missingReq) && $applicant->jamb_score >= $cutoff) {
                // Check Quota
                $admittedCount = \App\Models\Applicant::where('course_applied', $applicant->course_applied)
                                                    ->where('status', 'admitted')
                                                    ->count();
                
                if ($admittedCount < ($course->quota ?? 100)) {
                    $status = 'admitted';
                    $message = "Congratulations! Provisional Admission Offered (Score: $finalScore).";
                    $reason = "Met all requirements within quota.";
                } else {
                     // Waitlist Logic
                    $status = 'waitlisted'; 
                    $message = "Qualified but Quota Full. You have been placed on the Waitlist.";
                    $reason = "Qualified (Score: $finalScore) but Course Quota Full.";
                }
            } elseif (!empty($missingReq)) {
                 $message = "Declined. Missing Credits: " . implode(', ', $missingReq);
                 $reason = "Missing Required Subjects: " . implode(', ', $missingReq);
            } elseif ($applicant->jamb_score < $cutoff) {
                 $reason = "JAMB Score ($applicant->jamb_score) below departmental cutoff ($cutoff).";
            }

            $applicant->update([
                'status' => $status,
                'reason' => $reason
            ]);
            $applicant->notify(new \App\Notifications\AdmissionDecision($status, $message));

            $processed++;
        }
        
        // Remove duplicate loop finisher (bug fix)
        return response()->json([
            'message' => "Standardized Admission Process Complete.",
            'processed' => $processed,
            'success' => true
        ], 200);
    }

    public function manualAdmit($id)
    {
        $applicant = Applicant::findOrFail($id);
        $applicant->update([
            'status' => 'admitted',
            'created_at' => now(), // Refresh timestamp to show at top? Optional.
        ]);
        $applicant->notify(new \App\Notifications\AdmissionDecision('admitted', "Congratulations! Your admission has been manually approved by the administration."));
        
        return back()->with('success', "Applicant {$applicant->full_name} has been manually Admitted.");
    }

    public function manualReject($id)
    {
        $applicant = Applicant::findOrFail($id);
        $applicant->update(['status' => 'rejected']);
        $applicant->notify(new \App\Notifications\AdmissionDecision('rejected', "Admission Declined regarding your review."));
        
        return back()->with('success', "Applicant {$applicant->full_name} has been Rejected.");
    }

    private function getGradePoints($grade)
    {
        $grading = ['A1' => 6, 'B2' => 5, 'B3' => 4, 'C4' => 3, 'C5' => 2, 'C6' => 1];
        return $grading[strtoupper($grade)] ?? 0;
    }

    private function calculateOLevelPoints($olevel)
    {
        if (!$olevel || !is_array($olevel)) return 0;
        $total = 0;
        $count = 0;

        foreach ($olevel as $key => $grade) {
            if ($count >= 5) break; 

            if (is_array($grade)) { $grade = $grade['grade'] ?? ''; }
            
            $total += $this->getGradePoints($grade);
            $count++;
        }
        return $total;
    }
}
