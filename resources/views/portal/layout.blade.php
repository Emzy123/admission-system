<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Portal - Admission System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; }
        .hero-header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 0; }
        .portal-card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-control:focus { border-color: #2a5298; box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.25); }
    </style>
</head>
<body>
    <div class="hero-header mb-5">
        <div class="container d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-graduation-cap me-2"></i> Applicant Portal</h3>
            @auth('applicant')
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ Auth::guard('applicant')->user()->full_name }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ url('/portal/dashboard') }}">Dashboard</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ url('/portal/logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </div>

    <div class="container pb-5">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
