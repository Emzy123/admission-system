@extends('portal.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Notifications -->
        @if($applicant->notifications->count() > 0)
            @foreach($applicant->notifications as $notification)
                <div class="alert alert-{{ $notification->data['status'] == 'admitted' ? 'success' : 'danger' }} shadow-sm border-0 mb-4">
                    <h4 class="alert-heading"><i class="fas fa-envelope-open-text me-2"></i>Admission Update</h4>
                    <p class="mb-0">{{ $notification->data['message'] }}</p>
                    <hr>
                    <p class="mb-0 small text-muted">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        @endif

        <div class="card portal-card mb-4 text-center p-4">
             <div class="mb-3">
                @if($applicant->passport_photo)
                    <img src="{{ asset('storage/' . $applicant->passport_photo) }}" class="rounded-circle" width="100" height="100">
                @else
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 40px;">
                        {{ substr($applicant->full_name, 0, 1) }}
                    </div>
                @endif
             </div>
             <h5>{{ $applicant->full_name }}</h5>
             <p class="text-muted">{{ $applicant->email }}</p>
             <hr>
             @if($applicant->is_submitted)
                 <div class="d-grid">
                     <button class="btn btn-outline-secondary" disabled>Application Submitted</button>
                 </div>
             @else
                 <div class="d-grid">
                     <a href="{{ url('/portal/apply') }}" class="btn btn-primary">Complete Application</a>
                 </div>
             @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="card portal-card p-4">
            <h4 class="mb-4">Application Status</h4>

            @if(!$applicant->is_submitted)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> You have not submitted your application yet. Please complete the form to be considered for admission.
                </div>
            @else
                <div class="row mb-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block uppercase">Course Applied</small>
                        <span class="fs-5 fw-bold">{{ $applicant->course_applied }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block uppercase">Current Status</small>
                        @if($applicant->status == 'pending')
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">Pending Review</span>
                        @elseif($applicant->status == 'admitted')
                            <span class="badge bg-success fs-6 px-3 py-2">Congratulation! Admitted</span>
                        @else
                            <span class="badge bg-danger fs-6 px-3 py-2">Not Admitted</span>
                        @endif
                    </div>
                </div>
                
                @if($applicant->status == 'admitted')
                    <div class="alert alert-success border-0 bg-success text-white">
                        <h4>Congratulations!</h4>
                        <p>You have been offered provisional admission into the department of <strong>{{ $applicant->course_applied }}</strong>.</p>
                        <button class="btn btn-light btn-sm mt-2 text-success fw-bold">Print Admission Letter</button>
                    </div>
                @elseif($applicant->status == 'rejected')
                     <div class="alert alert-danger border-0">
                        <p>We regret to inform you that you were not offered admission this session. Changes in departmental cut-off marks and quota limitations affected your application.</p>
                    </div>
                @else
                    <div class="step-progress mt-5">
                         <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 50%;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2 text-muted small">
                            <span>Registration</span>
                            <span class="text-success fw-bold">Processing</span>
                            <span>Decision</span>
                        </div>
                    </div>
                @endif

            @endif
        </div>
    </div>
</div>
@endsection
