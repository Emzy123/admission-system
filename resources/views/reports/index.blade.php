@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Admissions Reports</h2>
        <div class="alert alert-light border shadow-sm mt-3">
             <i class="fas fa-chart-line me-2"></i>
             Visual insights into the admission process.
        </div>
    </div>
</div>

<div class="row">
    <!-- Success Rate Chart -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white font-weight-bold">
                Admission Success Rate
            </div>
            <div class="card-body">
                <canvas id="successRateChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Department Distribution Chart -->
    <div class="col-md-6 mb-4">
         <div class="card h-100 shadow-sm">
            <div class="card-header bg-white font-weight-bold">
                Intake by Department
            </div>
            <div class="card-body">
                <canvas id="deptChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
         <div class="card shadow-sm">
            <div class="card-header bg-white font-weight-bold">
                Rejection Analysis
            </div>
            <div class="card-body">
                <canvas id="rejectionChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Success Rate
        const ctx1 = document.getElementById('successRateChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Admitted', 'Rejected'],
                datasets: [{
                    data: [{{ $admittedCount }}, {{ $rejectedCount }}], 
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            }
        });

        // Department Distribution
        const ctx2 = document.getElementById('deptChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: {!! json_encode($deptLabels) !!},
                datasets: [{
                    label: 'Admitted Students',
                    data: {!! json_encode($deptData) !!},
                    backgroundColor: '#007bff'
                }]
            },
             options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                }
            }
        });

        // Rejection Reasons (Simplified for now as we track status only)
        const ctx3 = document.getElementById('rejectionChart').getContext('2d');
        new Chart(ctx3, {
            type: 'horizontalBar', 
            data: {
                labels: ['Below Criteria'],
                datasets: [{
                    label: 'Rejected Applicants',
                    data: [{{ $rejectedCount }}],
                    backgroundColor: '#ffc107'
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                }
            }
        });
    });
</script>
@endsection
