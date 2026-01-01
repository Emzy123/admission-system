<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automated Admission Decision System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #2c3e50;
            color: #ecf0f1;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding-top: 20px;
            overflow-y: auto;
        }
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 16px;
            color: #ecf0f1;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #34495e;
            color: #ffffff;
            border-left: 4px solid #3498db;
        }
        .sidebar .brand {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            color: #ecf0f1;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .header {
            background-color: #ffffff;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .system-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .academic-session {
            font-weight: bold;
            color: #7f8c8d;
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }
        .btn-success {
            background-color: #27ae60;
            border-color: #27ae60;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <i class="fas fa-university me-2"></i> UNI-PORTAL
        </div>
        <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a href="{{ url('/applicants/upload') }}" class="{{ request()->is('applicants/upload') ? 'active' : '' }}">
            <i class="fas fa-upload me-2"></i> Upload Applicants
        </a>
        <a href="{{ url('/admission/rules') }}" class="{{ request()->is('admission/rules') ? 'active' : '' }}">
            <i class="fas fa-gavel me-2"></i> Admission Rules
        </a>
        <a href="{{ url('/admission/process') }}" class="{{ request()->is('admission/process') ? 'active' : '' }}">
            <i class="fas fa-cogs me-2"></i> Process Admission
        </a>
        <a href="{{ url('/admission/results') }}" class="{{ request()->is('admission/results') ? 'active' : '' }}">
            <i class="fas fa-list-alt me-2"></i> Admission Results
        </a>
        <a href="{{ url('/jamb/confirmation') }}" class="{{ request()->is('jamb/confirmation') ? 'active' : '' }}">
            <i class="fas fa-globe me-2"></i> JAMB Confirmation
        </a>
        <a href="{{ url('/reports') }}" class="{{ request()->is('reports') ? 'active' : '' }}">
            <i class="fas fa-chart-bar me-2"></i> Reports
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="header">
            <div class="system-name">
                 Automated Admission Decision System
            </div>
            <div class="academic-session">
                Session: <select class="form-select d-inline-block w-auto ms-2">
                    <option>2024/2025</option>
                    <option>2023/2024</option>
                </select>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
