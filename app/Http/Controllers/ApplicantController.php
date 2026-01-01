<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    public function index()
    {
        $applicants = Applicant::latest()->get();
        return view('applicants.index', compact('applicants'));
    }

    public function create()
    {
        // View for manually adding applicant
    }

    public function store(Request $request)
    {
        // Store logic
    }
}
