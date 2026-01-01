@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Admission Results</h2>
        <div class="alert alert-success border-0 shadow-sm mt-3">
             <i class="fas fa-check-circle me-2"></i>
             Admission processing complete. Results generated based on system rules.
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" id="admissionTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-success fw-bold" id="admitted-tab" data-bs-toggle="tab" href="#admitted" role="tab">
                    <i class="fas fa-check me-2"></i>Admitted Applicants
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger fw-bold" id="rejected-tab" data-bs-toggle="tab" href="#rejected" role="tab">
                    <i class="fas fa-times me-2"></i>Not Admitted
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="admissionTabsContent">
            <!-- Admitted Tab -->
            <div class="tab-pane fade show active" id="admitted" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>JAMB Reg</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Score</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Demo Data -->
                            <!-- Real Data -->
                            @forelse($admitted as $a)
                            <tr>
                                <td>{{ $a->jamb_reg_no }}</td>
                                <td>{{ $a->full_name }}</td>
                                <td>{{ $a->course_applied }}</td>
                                <td>{{ $a->jamb_score }}</td>
                                <td><span class="badge bg-success">Admitted</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No admitted applicants yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rejected Tab -->
            <div class="tab-pane fade" id="rejected" role="tabpanel">
                 <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>JAMB Reg</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Score</th>
                                <th>Reason for Rejection</th>
                            </tr>
                        </thead>
                        <tbody>
                             <!-- Demo Data -->
                             <!-- Real Data -->
                             @forelse($rejected as $r)
                            <tr>
                                <td>{{ $r->jamb_reg_no }}</td>
                                <td>{{ $r->full_name }}</td>
                                <td>{{ $r->course_applied }}</td>
                                <td>{{ $r->jamb_score }}</td>
                                <td><span class="text-danger fw-bold">Below Criteria</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No rejected applicants.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
