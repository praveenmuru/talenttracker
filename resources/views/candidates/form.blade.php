<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">First Name</label>
        <input type="text" name="first_name" value="{{ old('first_name', $candidate->first_name ?? '') }}" class="form-control border-dark rounded-0">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Last Name</label>
        <input type="text" name="last_name" value="{{ old('last_name', $candidate->last_name ?? '') }}" class="form-control border-dark rounded-0">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Email</label>
        <input type="email" name="email" value="{{ old('email', $candidate->email ?? '') }}" class="form-control border-dark rounded-0">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $candidate->phone ?? '') }}" class="form-control border-dark rounded-0">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Experience (Years)</label>
        <input type="number" step="0.1" name="experience" value="{{ old('experience', $candidate->experience ?? '') }}" class="form-control border-dark rounded-0">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Skills</label>
        <input type="text" name="skills" value="{{ old('skills', $candidate->skills ?? '') }}" class="form-control border-dark rounded-0">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Opening Opening</label>
        <select name="job_id" class="form-select border-dark rounded-0">
            <option value="">Select Opening</option>
            @foreach($openings as $opening)
                <option value="{{ $opening->id }}" {{ old('job_id', $candidate->job_id ?? '') == $opening->id ? 'selected' : '' }}>{{ $opening->title }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Status</label>
        <select name="status" class="form-select border-dark rounded-0">
            @foreach(['New','Shortlisted','Interviewed','Hired','Rejected'] as $s)
                <option value="{{ $s }}" {{ old('status', $candidate->status ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12">
        <label class="form-label fw-bold">Upload Resume</label>
        <!-- <input type="file" name="resume" class="form-control border-dark rounded-0">
        @if(isset($candidate->resume_path))
            <small class="text-muted">Current: <a href="{{ asset('storage/'.$candidate->resume_path) }}" target="_blank">View</a></small>
        @endif -->

        <!-- @if(isset($candidate->resume_path) && $candidate->resume_path)
        <input type="hidden" name="resume_path" value="{{ $candidate->resume_path }}">
        <div class="form-control border-dark rounded-0 bg-light">
            File already uploaded: <a href="{{ asset('storage/'.$candidate->resume_path) }}" target="_blank">{{ $candidate->resume_path }}</a>
        </div>
        <small class="text-muted">You can upload a different file below to replace it.</small>
        <input type="file" name="resume" class="form-control border-dark rounded-0 mt-2">
    @else
        <input type_file" name="resume" class="form-control border-dark rounded-0">
    @endif -->
    </div>
    <div class="col-12">
        <label class="form-label fw-bold">Notes</label>
        <textarea name="notes" class="form-control border-dark rounded-0" rows="3">{{ old('notes', $candidate->notes ?? '') }}</textarea>
    </div>

    @if(isset($candidate->resume_path))
    <input type="hidden" name="resume_path" value="{{ $candidate->resume_path }}">
    <div class="col-12">
        <small class="text-success">Resume file: {{ $candidate->resume_path }} (already uploaded)</small>
    </div>
@endif
</div>

<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn text-white rounded-0 px-4" style="background-color:#f97316;">Save</button>
    <a href="{{ route('candidates.index') }}" class="btn btn-outline-dark rounded-0 ms-3 px-4">Cancel</a>
</div>
