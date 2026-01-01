@extends('layouts.app')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Admission Processing</h2>
                <p class="text-muted mb-0">Review pending applicants before processing.</p>
            </div>
            
            <div>
                <button type="button" id="processBtn" class="btn btn-primary btn-lg shadow" onclick="runAdmission()">
                    <i class="fas fa-cogs me-2"></i> Process ALL Admission Rules
                </button>
                <div id="processSpinner" class="d-none text-end mt-2">
                    <div class="spinner-border text-primary" role="status"></div>
                    <span class="ms-2 text-primary fw-bold">Processing... Please Wait...</span>
                </div>
            </div>
        </div>

        <script>
            function runAdmission() {
                if(!confirm('Are you sure? This will update admission status for all pending applicants.')) return;

                const btn = document.getElementById('processBtn');
                const spinner = document.getElementById('processSpinner');
                
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spin fa-spinner me-2"></i> Processing...';
                spinner.classList.remove('d-none');

                fetch('{{ url("/admission/run") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message + "\nEvaluated: " + data.processed);
                    window.location.href = '{{ url("/admission/results") }}';
                })
                .catch(error => {
                    alert("Error: " + error);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Retry';
                    spinner.classList.add('d-none');
                });
            }
        </script>

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
