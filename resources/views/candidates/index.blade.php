@extends('layouts.app')

@section('content')

<div class="container-fluid px-5 py-4 bg-light">
 <h3 class="fw-bold text-dark">Or, Add Candidate by Resume</h3>
<form action="{{ route('candidates.parse') }}" method="POST" enctype="multipart/form-data" class="row g-2 mb-4">
    @csrf
    <div class="col-md-4">
        <label for="resume_file" class="form-label">Upload Resume (PDF, DOCX)</label>
        <input type="file" name="resume_file" class="form-control" required>
    </div>
    <div class="col-md-2" style="margin-top: 2.2rem;">
        <button type="submit" class="btn text-white bg-primary w-100">Parse & Add</button>
    </div>
</form>

<hr/>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-dark">Candidates</h2>
        <a href="{{ route('candidates.create') }}" class="btn text-white rounded-0 px-4 bg-primary" >+ Add Candidate</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="opening_id" class="form-select rounded-0 border-dark">
                <option value="">All Openings</option>
                @foreach($openings as $opening)
                    <option value="{{ $opening->id }}" {{ request('opening_id')==$opening->id?'selected':'' }}>{{ $opening->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select rounded-0 border-dark">
                <option value="">All Status</option>
                @foreach(['New','Shortlisted','Interviewed','Hired','Rejected'] as $status)
                    <option value="{{ $status }}" {{ request('status')==$status?'selected':'' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn text-white rounded-0 w-100 bg-primary">Filter</button>
        </div>
    </form>

    <table class="table table-bordered border-dark align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Opening</th>
                <th>Status</th>
                <th>Resume</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($candidates as $c)
            <tr>
                <td>{{ $c->first_name }} {{ $c->last_name }}</td>
                <td>{{ $c->email }}</td>
                <td>{{ $c->opening->title ?? '—' }}</td>
                <td><span class="badge bg-dark text-white">{{ $c->status }}</span></td>
                <td>
                    @if($c->resume_path)
                        <a href="{{ asset('storage/'.$c->resume_path) }}" target="_blank" class="text-decoration-none text-dark">View</a>
                    @else —
                    @endif
                </td>
                <td>
                    <a href="{{ route('candidates.edit', $c->id) }}" class="btn btn-sm text-white rounded-0 bg-primary">Edit</a>
                    <form action="{{ route('candidates.destroy', $c->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-dark rounded-0">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6">No candidates found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $candidates->links() }}
</div>
@endsection
