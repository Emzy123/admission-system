<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmittedCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'jamb_reg_no',
        'full_name',
        'course_admitted',
        'gender',
        'state_of_origin',
        'jamb_score',
        'status',
        'admitted_at'
    ];
}
