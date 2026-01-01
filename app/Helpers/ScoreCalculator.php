<?php

namespace App\Helpers;

class ScoreCalculator {
    public static function calculate($jamb, $olevelPercent) {
        return ($jamb / 400) * 50 + ($olevelPercent / 100) * 50;
    }
}
