@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Upload Applicants</h2>
        <div class="alert alert-warning border-0 shadow-sm mt-3">
            <i class="fas fa-exclamation-circle me-2"></i>
            Uploaded applicants will be automatically evaluated against admission rules.
        </div>
    </div>
</div>

<div class="row">
    <!-- CSV Upload -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-file-csv me-2 text-success"></i> Bulk Upload (JAMB Export)</h5>
            </div>
            <div class="card-body">
                <form id="csvUploadForm" onsubmit="uploadCSV(event)">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select CSV File</label>
                        <input type="file" name="csv_file" id="csvFile" class="form-control" required accept=".csv,.xlsx">
                        <div class="form-text">
                            <strong>Format:</strong> Official JAMB Export (29 Columns).<br>
                            <em>(RegNo, Name, Gender, State, LGA, Score, Subjects...)</em>
                        </div>
                    </div>
                    
                    <!-- Progress Bar Container (Hidden by default) -->
                    <div id="progressContainer" class="mb-3 d-none">
                        <div class="progress" style="height: 25px;">
                            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%">0%</div>
                        </div>
                        <small id="progressText" class="text-muted d-block mt-1">Starting upload...</small>
                    </div>

                    <button type="submit" id="uploadBtn" class="btn btn-success w-100"><i class="fas fa-upload me-2"></i> Upload Applicants</button>
                </form>

                <script>
                    function uploadCSV(e) {
                        e.preventDefault();
                        
                        const fileInput = document.getElementById('csvFile');
                        const file = fileInput.files[0];
                        if (!file) return;

                        const formData = new FormData();
                        formData.append('csv_file', file);
                        formData.append('_token', '{{ csrf_token() }}');

                        const xhr = new XMLHttpRequest();
                        const progressBar = document.getElementById('uploadProgressBar');
                        const progressContainer = document.getElementById('progressContainer');
                        const progressText = document.getElementById('progressText');
                        const btn = document.getElementById('uploadBtn');

                        // UI Reset
                        progressContainer.classList.remove('d-none');
                        btn.disabled = true;
                        progressBar.style.width = '0%';
                        progressBar.innerText = '0%';
                        progressBar.classList.remove('bg-danger');
                        progressBar.classList.add('bg-success');

                        // Upload Progress
                        xhr.upload.onprogress = function(event) {
                            if (event.lengthComputable) {
                                const percent = Math.round((event.loaded / event.total) * 100);
                                progressBar.style.width = percent + '%';
                                progressBar.innerText = percent + '%';
                                progressText.innerText = 'Uploading... ' + percent + '%';
                            }
                        };

                        // State Change
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4) {
                                btn.disabled = false;
                                if (xhr.status === 200) {
                                    try {
                                        const response = JSON.parse(xhr.responseText);
                                        progressBar.style.width = '100%';
                                        progressBar.innerText = 'Complete';
                                        
                                        // Show detailed success message
                                        progressText.innerHTML = `
                                            <div class="alert alert-success mt-2">
                                                <strong><i class="fas fa-check-circle"></i> Success!</strong><br>
                                                ${response.message}<br>
                                                <small>Inserted: ${response.inserted} | Skipped: ${response.skipped}</small>
                                            </div>
                                            <div class="text-center text-muted">Reloading page...</div>
                                        `;
                                        
                                        setTimeout(() => window.location.reload(), 3000);
                                    } catch (e) {
                                        // Fallback if not JSON
                                        progressBar.innerText = 'Done';
                                        progressText.innerText = 'Upload Completed. Reloading...';
                                        setTimeout(() => window.location.reload(), 1000);
                                    }
                                } else {
                                    progressBar.classList.remove('bg-success');
                                    progressBar.classList.add('bg-danger');
                                    progressBar.innerText = 'Error';
                                    progressText.innerText = 'Upload Failed: ' + (xhr.responseText || 'Server Error');
                                    console.error(xhr.responseText);
                                }
                            }
                        };

                        xhr.open('POST', '{{ url("/admin/applicants/csv") }}', true);
                        xhr.send(formData);
                    }
                </script>
            </div>
        </div>
    </div>

    <!-- Manual Entry (Demo) -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2 text-primary"></i> Manual Entry</h5>
            </div>
            <div class="card-body">
                <form action="{{ url('/admin/applicants/manual') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">JAMB Reg No</label>
                            <input type="text" name="jamb_reg_no" class="form-control" placeholder="2024..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">JAMB Score</label>
                            <input type="number" name="jamb_score" class="form-control" placeholder="0-400" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                         <label class="form-label">Email Address <span class="text-danger">*</span></label>
                         <input type="email" name="email" class="form-control" required>
                         <div class="form-text">Required for applicant login access.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department Choice</label>
                            <select name="course_applied" class="form-select">
                                <option value="Computer Science">Computer Science</option>
                                <option value="Medicine">Medicine</option>
                                <option value="Law">Law</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Microbiology">Microbiology</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">State</label>
                            <select name="state" class="form-select">
                                <option value="Rivers">Rivers</option>
                                <option value="Lagos">Lagos</option>
                                <option value="Abuja">Abuja</option>
                                <option value="Enugu">Enugu</option>
                                <option value="Kano">Kano</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">O-Level Results (5 Subjects)</label>
                        <div class="bg-light p-3 rounded">
                            @for($i=0; $i<5; $i++)
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <select name="olevel[{{$i}}][subject]" class="form-select form-select-sm" required>
                                        <option value="" disabled selected>Select Subject</option>
                                        <option value="Mathematics">Mathematics</option>
                                        <option value="English">English</option>
                                        <option value="Physics">Physics</option>
                                        <option value="Chemistry">Chemistry</option>
                                        <option value="Biology">Biology</option>
                                        <option value="Economics">Economics</option>
                                        <option value="Government">Government</option>
                                        <option value="Literature">Literature</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select name="olevel[{{$i}}][grade]" class="form-select form-select-sm" required>
                                        <option value="" disabled selected>Grade</option>
                                        <option value="A1">A1 (Excellent)</option>
                                        <option value="B2">B2 (Very Good)</option>
                                        <option value="B3">B3 (Good)</option>
                                        <option value="C4">C4 (Credit)</option>
                                        <option value="C5">C5 (Credit)</option>
                                        <option value="C6">C6 (Credit)</option>
                                        <option value="D7">D7 (Pass)</option>
                                        <option value="E8">E8 (Pass)</option>
                                        <option value="F9">F9 (Fail)</option>
                                    </select>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                    
                    <input type="hidden" name="is_submitted" value="1">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus me-2"></i> Add Applicant</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
