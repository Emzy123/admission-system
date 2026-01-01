<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdmissionResult;

class JambController extends Controller
{
    // Mock Endpoint receiving confirmation request
    public function confirm(Request $request)
    {
        // In reality, this would be an external API endpoint
        return response()->json([
            'status' => 'confirmed',
            'reference' => 'JAMB-CAPS-OK'
        ]);
    }

    // Admin action to trigger checking/syncing status
    public function checkStatus($resultId)
    {
        $result = AdmissionResult::findOrFail($resultId);
        
        // Simulate API call
        // $response = Http::post('...');
        
        // Mock success
        $result->update(['jamb_status' => 'confirmed']);
        
        return back()->with('success', 'JAMB status confirmed for candidate.');
    }
}
