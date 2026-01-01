@extends('portal.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card portal-card p-4">
            <div class="text-center mb-4">
                <h4>Login to Your Account</h4>
                <p class="text-muted">Enter your email and password to continue</p>
            </div>

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100 py-2 fw-bold">Login</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ url('/register') }}" class="text-decoration-none">Don't have an account? Register here</a>
            </div>
        </div>
    </div>
</div>
@endsection
