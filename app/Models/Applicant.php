<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Applicant extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name', 'email', 'password', 'jamb_reg_no', 'jamb_score', 
        'olevel', 'state_of_origin', 'course_applied', 'aggregate', 
        'status', 'reason', 'is_submitted',
        'has_disciplinary_record', 'academic_trend', 
        'recommendation_score', 'hardship_bonus'
    ];

    protected $casts = [
        'olevel' => 'array',
        'password' => 'hashed',
        'is_submitted' => 'boolean'
    ];
}
