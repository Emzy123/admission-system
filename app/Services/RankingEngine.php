<?php

namespace App\Services;

class RankingEngine {
    public function rank($applicants) {
        return $applicants->sortByDesc('aggregate');
    }
}
