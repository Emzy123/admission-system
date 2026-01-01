<?php

namespace App\Services;

class RuleEngine {

    public function qualifies($applicant, $course) {

        if ($applicant->jamb_score < $course->cutoff) {
            return false;
        }

        $olevel = is_string($applicant->olevel) ? json_decode($applicant->olevel, true) : $applicant->olevel;
        $required = is_string($course->required_subjects) ? json_decode($course->required_subjects, true) : $course->required_subjects;

        // Start with a simpler check if json_decode fails or is null
        if (!$olevel || !$required) return false;

        foreach ($required as $subject) {
            // Case insensitive check might be needed, but sticking to basic implementation
            if (!isset($olevel[$subject])) {
                return false;
            }
        }

        return true;
    }
}
