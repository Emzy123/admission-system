@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-info border-0 shadow-sm">
            <i class="fas fa-info-circle me-2"></i>
            Admissions are generated automatically using predefined rules.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-center py-4 mb-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-3">Total Applicants</h6>
                <h2 class="display-4 fw-bold text-primary">{{ $metrics['total_applicants'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-4 mb-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-3">Pending / Eligible</h6>
                <h2 class="display-4 fw-bold text-info">{{ $metrics['eligible'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-4 mb-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-3">Admitted</h6>
                <h2 class="display-4 fw-bold text-success">{{ $metrics['admitted'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-center py-4 mb-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-3">Waitlisted</h6>
                <h2 class="display-4 fw-bold text-warning">{{ $metrics['waitlisted'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-4 mb-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-3">Under Review</h6>
                <h2 class="display-4 fw-bold text-danger">{{ $metrics['under_review'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-4 mb-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase mb-3">Rejected</h6>
                <h2 class="display-4 fw-bold text-muted">{{ $metrics['rejected'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12 text-center">
        <p class="text-muted">System is ready for 2024/2025 admission cycle.</p>
    </div>
</div>
@endsection
