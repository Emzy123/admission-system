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

            // Update Aggregate
            $applicant->update(['aggregate' => $finalAggregate]);

            // 2. Admission Decision
            // Logic: Admit if JAMB >= Cutoff AND O-Level Points >= 15
            $status = 'rejected';
            $message = "We regret to inform you that you did not meet the required criteria.";
            $missingReq = [];

            // 2a. Check Required Subjects (New Standard)
            if ($course && $course->required_subjects) {
                $requirements = is_string($course->required_subjects) ? json_decode($course->required_subjects, true) : $course->required_subjects;
                
                // Normalizing applicant subjects to lowercase for comparison
                $applicantSubjects = [];
                if (is_array($applicant->olevel)) {
                    foreach ($applicant->olevel as $k => $v) {
                         // Handling both ['Math'=>'A1'] and [{'subject'=>'Math', 'grade'=>'A1'}]
                         $subName = is_array($v) ? ($v['subject'] ?? '') : $k;
                         $subGrade = is_array($v) ? ($v['grade'] ?? '') : $v;
                         
                         if ($subName) $applicantSubjects[strtolower(trim($subName))] = strtoupper(trim($subGrade));
                    }
                }

                foreach ($requirements as $req) {
                    $req = strtolower(trim($req));
                    $grade = $applicantSubjects[$req] ?? 'F9';
                    
                    // Check if grade is a pass (A1-C6). F9, E8, D7 considered fail for Core Requirements.
                    // A1=6 ... C6=1. F9=0.
                    $points = $this->getGradePoints($grade);
                    if ($points < 1) { // Strict check: Must have at least C6
                        $missingReq[] = ucfirst($req);
                    }
                }
            }

            if ($applicant->jamb_score >= $cutoff && $oLevelPoints >= 15 && empty($missingReq)) {
                $status = 'admitted';
                $message = "Congratulations! You have been offered provisional admission into " . $applicant->course_applied;
            } elseif (!empty($missingReq)) {
                 $message = "Admission Declined. Missing Credit in required subjects: " . implode(', ', $missingReq);
            }

            $applicant->update(['status' => $status]);
            $applicant->notify(new \App\Notifications\AdmissionDecision($status, $message));

            $processed++;
        }

            $processed++;
        }

        return response()->json([
            'message' => "Standardized Admission Process Complete.",
            'processed' => $processed,
            'success' => true
        ], 200);
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
