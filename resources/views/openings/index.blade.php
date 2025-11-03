@extends('layouts.app')

@section('content')


<div class="container-fluid">
    <div class="container">
        <h1 class="mb-4">Opening Openings</h1>

        <form method="GET" class="mb-3 d-flex gap-2 flex-wrap">
            <input type="text" name="department" value="{{ request('department') }}" class="form-control" placeholder="Department">
            <input type="text" name="title" value="{{ request('title') }}" class="form-control" placeholder="Title">
            <select name="status" class="form-control">
                <option value="">All</option>
                <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
                <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
                <option value="archived" {{ request('status')=='archived'?'selected':'' }}>Archived</option>
            </select>
            <button class="btn btn-primary">Filter</button>
        </form>

        <a href="{{ route('openings.create') }}" class="btn btn-success mb-3">+ New Opening</a>

        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Salary</th>
                    <th>Expected Joining</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($openings as $opening)
                <tr>
                    <td>{{ $opening->title }}</td>
                    <td>{{ $opening->department }}</td>
                    <td>{{ ucfirst($opening->status) }}</td>
                    <td>{{ $opening->salary_min }} - {{ $opening->salary_max }}</td>
                    <td>{{ $opening->expected_joining_date }}</td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('openings.edit', $opening->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('openings.destroy', $opening->id) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No openings found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $openings->links() }}
        </div>
    </div>
</div>
@endsection
