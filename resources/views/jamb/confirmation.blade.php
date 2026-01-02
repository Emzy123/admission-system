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
                <h4 class="mb-4">Ready to submit <strong>{{ $admitted->count() }}</strong> admitted candidates?</h4>
                
                <div class="text-start mb-4">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Reg No</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Score</th>
                                    <th>Date Admitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admitted as $student)
                                <tr>
                                    <td>{{ $student->jamb_reg_no }}</td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->course_admitted }}</td>
                                    <td>{{ $student->jamb_score }}</td>
                                    <td>{{ $student->created_at->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No admitted candidates yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <button class="btn btn-primary btn-lg px-5" onclick="alert('Module connected to JAMB CAPS API. (Simulation)')">
                    <i class="fas fa-paper-plane me-2"></i> Submit List to JAMB
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
