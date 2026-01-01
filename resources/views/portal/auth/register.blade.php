@extends('portal.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card portal-card p-4">
            <div class="text-center mb-4">
                <h4>Create an Account</h4>
                <p class="text-muted">Start your admission journey today</p>
            </div>

            <form action="{{ url('/register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100 py-2 fw-bold">Register</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ url('/login') }}" class="text-decoration-none">Already have an account? Login here</a>
            </div>
        </div>
    </div>
</div>
@endsection
