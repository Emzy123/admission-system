@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Admission Management</h2>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Generate Admission List</div>
            <div class="card-body">
                <p>Select a course to run the admission engine.</p>
                <div class="list-group">
                    @foreach($courses as $course)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $course->name }}</strong> 
                            <span class="text-muted">(Cutoff: {{ $course->cutoff }}, Quota: {{ $course->quota }})</span>
                        </div>
                        <form action="{{ url('/admission/generate/' . $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Generate List</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h3>Admission Results</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Decision</th>
                    <th>JAMB Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                <tr>
                    <td>{{ $result->applicant->jamb_reg_no }}</td>
                    <td>{{ $result->applicant->full_name }}</td>
                    <td>{{ $result->course->name }}</td>
                    <td>
                        <span class="badge bg-{{ $result->decision == 'admitted' ? 'success' : 'danger' }}">
                            {{ ucfirst($result->decision) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $result->jamb_status == 'confirmed' ? 'success' : 'warning' }}">
                            {{ ucfirst($result->jamb_status) }}
                        </span>
                    </td>
                    <td>
                        @if($result->decision == 'admitted' && $result->jamb_status == 'pending')
                        <form action="{{ url('/jamb/confirm/' . $result->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Sync JAMB</button>
                        </form>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $results->links() }}
    </div>
</div>
@endsection
