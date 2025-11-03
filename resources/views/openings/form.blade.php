<div class="mb-3">
    <label>Title</label>
    <input type="text" name="title" value="{{ old('title', $opening->title) }}" class="form-control" required>
</div>

<div class="mb-3">
    <label>Department</label>
    <input type="text" name="department" value="{{ old('department', $opening->department) }}" class="form-control">
</div>

<div class="mb-3">
    <label>Description</label>
    <textarea name="description" class="form-control">{{ old('description', $opening->description) }}</textarea>
</div>

<div class="mb-3">
    <label>Requirements</label>
    <textarea name="requirements" class="form-control">{{ old('requirements', $opening->requirements) }}</textarea>
</div>

<div class="mb-3">
    <label>Expected Joining Date</label>
    <input type="date" name="expected_joining_date" value="{{ old('expected_joining_date', $opening->expected_joining_date) }}" class="form-control">
</div>

<div class="mb-3">
    <label>Salary Range</label>
    <div class="d-flex gap-2">
        <input type="number" name="salary_min" value="{{ old('salary_min', $opening->salary_min) }}" class="form-control" placeholder="Min">
        <input type="number" name="salary_max" value="{{ old('salary_max', $opening->salary_max) }}" class="form-control" placeholder="Max">
    </div>
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        @foreach(['open', 'closed', 'archived'] as $status)
            <option value="{{ $status }}" {{ old('status', $opening->status) == $status ? 'selected' : '' }}>
                {{ ucfirst($status) }}
            </option>
        @endforeach
    </select>
</div>
