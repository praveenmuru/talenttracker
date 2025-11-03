@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="container">
        <h1>Create Opening</h1>

        <form method="POST" action="{{ route('openings.store') }}">
            @csrf

            {{-- Grid layout for fields --}}
            
            <div class="form-grid">
                <div>
                    <label for="title">Opening Title</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Software Engineer" value="{{ old('title') }}">
                </div>

                <div>
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" class="form-control" placeholder="e.g. IT" value="{{ old('department') }}">
                </div>

                <div>
                    <label for="salary_min">Min Salary</label>
                    <input type="number" name="salary_min" id="salary_min" class="form-control" placeholder="e.g. 40000" value="{{ old('salary_min') }}">
                </div>

                <div>
                    <label for="salary_max">Max Salary</label>
                    <input type="number" name="salary_max" id="salary_max" class="form-control" placeholder="e.g. 70000" value="{{ old('salary_max') }}">
                </div>

                <div>
                    <label for="expected_joining_date">Expected Joining Date</label>
                    <input type="date" name="expected_joining_date" id="expected_joining_date" class="form-control" value="{{ old('expected_joining_date') }}">
                </div>

                <div>
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('openings.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
