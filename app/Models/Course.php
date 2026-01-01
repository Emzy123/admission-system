<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cutoff',
        'quota',
        'required_subjects',
        'catchment_states'
    ];

    protected $casts = [
        'required_subjects' => 'array',
        'catchment_states' => 'array',
    ];
}
