@extends('layouts.app')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Admission Processing</h2>
                <p class="text-muted mb-0">Review pending applicants before processing.</p>
            </div>
            
            <form action="{{ url('/admission/run') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg shadow" onclick="return confirm('Are you sure you want to run the admission rules? This will update statuses for all pending applicants.')">
                    <i class="fas fa-cogs me-2"></i> Process ALL Admission Rules
                </button>
            </form>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Pending Applicants Queued for Processing</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">JAMB Reg</th>
                                <th>Name</th>
                                <th>State</th>
                                <th>Course Applied</th>
                                <th>JAMB Score</th>
                                <th>Aggregate</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pending as $applicant)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $applicant->jamb_reg_no }}</td>
                                <td>{{ $applicant->full_name }}</td>
                                <td>{{ $applicant->state_of_origin }}</td>
                                <td><span class="badge bg-info text-dark">{{ $applicant->course_applied }}</span></td>
                                <td>{{ $applicant->jamb_score }}</td>
                                <td>{{ number_format($applicant->aggregate, 1) }}</td>
                                <td><span class="badge bg-warning text-dark">Pending Review</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                    <p class="mb-0">No pending applicants found. All caught up!</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light text-muted">
                Showing {{ $pending->count() }} pending applicants.
            </div>
        </div>
    </div>
</div>
@endsection
