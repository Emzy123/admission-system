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
            // Logic: Admit if JAMB >= Cutoff AND O-Level Points >= 15 (Average C credit)
            $status = 'rejected';
            $message = "We regret to inform you that you did not meet the required criteria.";

            if ($applicant->jamb_score >= $cutoff && $oLevelPoints >= 15) {
                $status = 'admitted';
                $message = "Congratulations! You have been offered provisional admission into " . $applicant->course_applied;
            }

            $applicant->update(['status' => $status]);
            $applicant->notify(new \App\Notifications\AdmissionDecision($status, $message));

            $processed++;
        }

        return redirect('/admission/results')->with('success', "Standardized Admission Process Complete. Evaluated $processed candidates using Weighted Scoring.");
    }

    private function calculateOLevelPoints($olevel)
    {
        if (!$olevel || !is_array($olevel)) return 0;

        $grading = ['A1' => 6, 'B2' => 5, 'B3' => 4, 'C4' => 3, 'C5' => 2, 'C6' => 1];
        $total = 0;
        $count = 0;

        foreach ($olevel as $grade) {
            if ($count >= 5) break; // Top 5 subjects
            $total += $grading[strtoupper($grade)] ?? 0;
            $count++;
        }
        return $total;
    }
}
}
