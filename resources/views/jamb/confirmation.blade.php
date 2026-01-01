@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>JAMB Confirmation</h2>
        <div class="alert alert-secondary border-0 shadow-sm mt-3">
             <i class="fas fa-server me-2"></i>
             Integration with JAMB Central Admission Processing System (CAPS).
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
             <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-cloud-upload-alt me-2"></i> Submit Admission List to JAMB</h5>
            </div>
            <div class="card-body text-center py-5">
                <h4 class="mb-4">Ready to submit <strong>3</strong> admitted candidates?</h4>
                
                <button class="btn btn-primary btn-lg px-5" data-bs-toggle="collapse" data-bs-target="#apiPreview">
                    <i class="fas fa-paper-plane me-2"></i> Send Admission List to JAMB
                </button>

                <!-- Simulated API Request Preview -->
                <div class="collapse mt-4 text-start" id="apiPreview">
                    <div class="card bg-dark text-white border-0">
                        <div class="card-header bg-secondary border-0">
                            <small>API Request Preview (Simulated)</small>
                        </div>
                        <div class="card-body">
                            <pre class="mb-0 text-success">
POST /api/v1/admissions/upload HTTP/1.1
Host: caps.jamb.gov.ng
Authorization: Bearer ********************
Content-Type: application/json

{
    "session": "2024/2025",
    "institution_code": "00123",
    "candidates": [
        {
            "jamb_reg": "20249823AB",
            "course_code": "CSC101",
            "score": 285
        },
        {
            "jamb_reg": "20241234CD",
            "course_code": "MED202",
            "score": 310
        },
        ...
    ]
}
                            </pre>
                        </div>
                        <div class="card-footer bg-secondary border-0 text-center">
                             <div class="spinner-grow spinner-grow-sm text-light" role="status"></div>
                             Sending data... (Simulated)
                        </div>
                    </div>
                     <div class="alert alert-success mt-3">
                        <i class="fas fa-check-double me-2"></i> <strong>Success:</strong> Data successfully synchronized with JAMB CAPS.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
