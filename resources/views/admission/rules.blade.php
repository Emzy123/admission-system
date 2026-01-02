@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Admission Rules</h2>
        <div class="alert alert-info border-0 shadow-sm mt-3">
             <i class="fas fa-info-circle me-2"></i>
             These rules are used by the system to automatically evaluate applicants. They are currently read-only in this prototype.
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Departmental Requirements</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Department</th>
                        <th>JAMB Cut-off</th>
                        <th>Required Subjects (O'Level)</th>
                        <th>Quota</th>
                        <th>Catchment Reserve</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">Computer Science</td>
                        <td><span class="badge bg-primary">200</span></td>
                        <td>Math, English, Physics, Chem, Bio</td>
                        <td>50</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Medicine & Surgery</td>
                        <td><span class="badge bg-primary">280</span></td>
                        <td>Math, English, Physics, Chem, Bio</td>
                        <td>30</td>
                        <td>5%</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Law</td>
                        <td><span class="badge bg-primary">250</span></td>
                        <td>Math, English, Lit., Govt, CRK/IRS</td>
                        <td>40</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Engineering</td>
                        <td><span class="badge bg-primary">180</span></td>
                        <td>Math, English, Physics, Chem, Tech Draw</td>
                        <td>100</td>
                        <td>20%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Holistic Review Rules -->
<div class="card shadow-sm mt-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-balance-scale me-2"></i>Holistic Review Criteria (New)</h5>
    </div>
    <div class="card-body">
        <p>In addition to academic scores, the system now evaluates candidates based on the following holistic factors:</p>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Factor</th>
                        <th>Condition</th>
                        <th>Impact on Score</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">Academic Trend</td>
                        <td>Consistent Improvement (Upward Trend)</td>
                        <td><span class="badge bg-success">+2 Points</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Recommendation</td>
                        <td>Strong Letter of Support (> 7/10)</td>
                        <td><span class="badge bg-success">+2 Points</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Institutional Priority</td>
                        <td>Catchment State Origin</td>
                        <td><span class="badge bg-success">+5 Points</span></td>
                    </tr>
                     <tr>
                        <td class="fw-bold text-danger">Disciplinary Record</td>
                        <td>Any record of misconduct</td>
                        <td><span class="badge bg-danger">Flagged for Review</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">General Rules</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Minimum Age
                    <span class="badge bg-secondary rounded-pill">16 Years</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    O'Level Sitting limit
                    <span class="badge bg-secondary rounded-pill">Max 2 Sittings</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    JAMB Validity
                    <span class="badge bg-secondary rounded-pill">Current Year Only</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
