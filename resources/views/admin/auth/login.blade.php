<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Admission System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .brand {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
            font-weight: bold;
            font-size: 24px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand">University Admin</div>
    
    @if($errors->any())
        <div class="alert alert-danger text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ url('/admin/login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="admin@university.edu.ng" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="******" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">Sign In to Dashboard</button>
    </form>
</div>

</body>
</html>
