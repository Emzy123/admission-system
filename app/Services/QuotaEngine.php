<?php

namespace App\Services;

class QuotaEngine {
    public function apply($ranked, $quota) {
        return $ranked->take($quota);
    }
}
