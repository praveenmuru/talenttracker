@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="container">
        <h1>Edit Opening</h1>

        <form method="POST" action="{{ route('openings.update', $opening->id) }}">
            @csrf
            @method('PUT')

            {{-- Form grid layout --}}
            <div class="form-grid">
                @include('openings.form')
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('openings.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
