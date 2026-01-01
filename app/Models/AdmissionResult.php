<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'course_id',
        'decision',
        'jamb_status'
    ];
    
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
    
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
