@extends('layouts.app')

@section('content')

<div class="container-fluid px-5 py-4 bg-light">

    <div class="row g-3 align-items-end mb-4">
        <div class="col-auto">
            <a href="{{ route('candidates.create') }}" class="btn text-white rounded-0 px-4 bg-primary" >+ Add Candidate</a>
        </div>

        <div class="col-auto">
             <span class="text-muted fst-italic px-2">or</span>
        </div>

        <div class="col-auto">
            <form action="{{ route('candidates.parse') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                @csrf
                <div class="col-auto">
                    <input type="file" name="resume_file" class="form-control" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn text-white bg-primary">Parse & Add</button>
                </div>
            </form>
        </div>
    </div>
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="job_id" class="form-select rounded-0 border-dark">
                <option value="">All Openings</option>
                @foreach($openings as $opening)
                    <option value="{{ $opening->id }}" {{ request('job_id')==$opening->id?'selected':'' }}>{{ $opening->title }}</option>
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