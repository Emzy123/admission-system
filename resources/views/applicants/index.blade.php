@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Applicants List</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>JAMB Reg No</th>
                    <th>Name</th>
                    <th>Score</th>
                    <th>Course Applied</th>
                    <th>Aggregate</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applicants as $applicant)
                <tr>
                    <td>{{ $applicant->jamb_reg_no }}</td>
                    <td>{{ $applicant->full_name }}</td>
                    <td>{{ $applicant->jamb_score }}</td>
                    <td>{{ $applicant->course_applied }}</td>
                    <td>{{ $applicant->aggregate }}</td>
                    <td>
                        <span class="badge bg-{{ $applicant->status == 'admitted' ? 'success' : ($applicant->status == 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($applicant->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
