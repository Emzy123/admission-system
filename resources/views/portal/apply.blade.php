@extends('portal.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card portal-card p-4">
            <h4 class="mb-4">Admission Application Form ({{ date('Y') }}/{{ date('Y')+1 }})</h4>
            
            <form action="{{ url('/portal/apply') }}" method="POST">
                @csrf

                <!-- JAMB Details -->
                <h6 class="text-primary mb-3">JAMB Details</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">JAMB Reg Number</label>
                        <input type="text" name="jamb_reg_no" class="form-control" placeholder="e.g. 20241234AB" value="{{ old('jamb_reg_no', $applicant->jamb_reg_no) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">JAMB Score</label>
                        <input type="number" name="jamb_score" class="form-control" placeholder="0-400" value="{{ old('jamb_score', $applicant->jamb_score) }}" required>
                    </div>
                </div>

                <!-- Personal Data -->
                <h6 class="text-primary mb-3 mt-4">Personal Data</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">State of Origin</label>
                        <select name="state_of_origin" class="form-select" required>
                            <option value="">Select State...</option>
                            <option value="Lagos">Lagos</option>
                            <option value="Rivers">Rivers</option>
                            <option value="Abuja">Abuja</option>
                            <option value="Delta">Delta</option>
                            <!-- Demo states -->
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Course of Choice</label>
                        <select name="course_applied" class="form-select" required>
                             <option value="">Select Course...</option>
                             @foreach($courses as $course)
                                <option value="{{ $course->name }}">{{ $course->name }}</option>
                             @endforeach
                        </select>
                    </div>
                </div>

                <!-- O-Level Results -->
                <h6 class="text-primary mb-3 mt-4">O-Level Results (5 Credits Required)</h6>
                <div class="alert alert-info py-2 small">
                    <i class="fas fa-info-circle me-1"></i> Enter your grades for 5 key subjects including Math and English.
                </div>
                
                <div id="subject-container">
                    @for($i=0; $i<5; $i++)
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <select name="olevel[{{$i}}][subject]" class="form-select" required>
                                <option value="">Select Subject...</option>
                                <option>Mathematics</option>
                                <option>English Language</option>
                                <option>Physics</option>
                                <option>Chemistry</option>
                                <option>Biology</option>
                                <option>Literature</option>
                                <option>Government</option>
                                <option>Economics</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                             <select name="olevel[{{$i}}][grade]" class="form-select" required>
                                <option value="">Select Grade...</option>
                                <option value="A1">A1</option>
                                <option value="B2">B2</option>
                                <option value="B3">B3</option>
                                <option value="C4">C4</option>
                                <option value="C5">C5</option>
                                <option value="C6">C6</option>
                            </select>
                        </div>
                    </div>
                    @endfor
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" required id="declaration">
                        <label class="form-check-label small" for="declaration">
                            I hereby declare that the information provided above is true and correct. I understand that falsification of documents will lead to automatic disqualification.
                        </label>
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold">Submit Application</button>
                    <p class="text-center text-muted small mt-2">Note: You cannot edit this after submission.</p>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
