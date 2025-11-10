
<div class="container-fluid py-4 px-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">{{ $candidate->exists ? 'Edit' : 'Create' }} Candidate</h5>
                </div>
                <div class="card-body">
                    <form action="{{ $candidate->exists ? route('candidates.update', $candidate->id) : route('candidates.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($candidate->exists)
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control border border-dark p-2" value="{{ old('first_name', $candidate->first_name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control border border-dark p-2" value="{{ old('last_name', $candidate->last_name) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control border border-dark p-2" value="{{ old('email', $candidate->email) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="job_id" class="form-label">Opening</label>
                            <select name="job_id" id="job_id" class="form-select border border-dark p-2">
                                <option value="">Select Opening</option>
                                @foreach($openings as $opening)
                                    <option value="{{ $opening->id }}" {{ old('job_id', $candidate->job_id) == $opening->id ? 'selected' : '' }}>
                                        {{ $opening->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select border border-dark p-2" required>
                                @foreach(['New', 'Shortlisted', 'Interviewed', 'Hired', 'Rejected'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $candidate->status) == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="resume_file" class="form-label">Resume (Optional)</label>
                            <input type="file" name="resume_file" id="resume_file" class="form-control border border-dark p-2">
                            @if($candidate->resume_path)
                                <small>Current: <a href="{{ asset('storage/'.$candidate->resume_path) }}" target="_blank">View Resume</a></small>
                            @endif
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn bg-primary text-white">{{ $candidate->exists ? 'Update' : 'Save' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>