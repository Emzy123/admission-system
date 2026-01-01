<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PortalController extends Controller
{
    // Auth
    public function showLogin() { return view('portal.auth.login'); }
    public function showRegister() { return view('portal.auth.register'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('applicant')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/portal/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required',
            'email' => 'required|email|unique:applicants',
            'password' => 'required|confirmed|min:6',
        ]);

        $applicant = Applicant::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('applicant')->login($applicant);

        return redirect('/portal/apply');
    }

    public function logout(Request $request)
    {
        Auth::guard('applicant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Application Process
    public function dashboard()
    {
        $applicant = Auth::guard('applicant')->user();
        return view('portal.dashboard', compact('applicant'));
    }

    public function showApplyForm()
    {
        $applicant = Auth::guard('applicant')->user();
        if ($applicant->is_submitted) {
            return redirect('/portal/dashboard');
        }
        $courses = Course::all();
        return view('portal.apply', compact('courses', 'applicant'));
    }

    public function submitApplication(Request $request)
    {
        $applicant = Auth::guard('applicant')->user();
        
        $validated = $request->validate([
            'jamb_reg_no' => 'required|unique:applicants,jamb_reg_no,' . $applicant->id,
            'jamb_score' => 'required|numeric|min:0|max:400',
            'state_of_origin' => 'required',
            'course_applied' => 'required',
            'olevel' => 'required|array|min:5',
        ]);

        // Simple Aggregate Calc (Mock logic: JAMB/8 + count(Olevel)*4) - simplified
        $aggregate = ($validated['jamb_score'] / 8) + 30; // Base score

        $applicant->update([
            'jamb_reg_no' => $validated['jamb_reg_no'],
            'jamb_score' => $validated['jamb_score'],
            'state_of_origin' => $validated['state_of_origin'],
            'course_applied' => $validated['course_applied'], // Name string for now to match backend
            'olevel' => $validated['olevel'],
            'aggregate' => $aggregate,
            'is_submitted' => true,
            'status' => 'pending'
        ]);

        return redirect('/portal/dashboard')->with('success', 'Application submitted successfully!');

    }
}
