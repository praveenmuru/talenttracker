@extends('layouts.app')

@section('content')

<div class="container-fluid px-5 py-4 bg-light">
    <div class="card border-0 shadow-sm rounded-0">
        <div class="card-header text-white rounded-0 bg-primary">
            <h4 class="mb-0">Edit Candidate</h4>
        </div>

        <div class="card-body bg-white">
            <form method="POST" action="{{ route('candidates.update', $candidate->id) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('candidates.form')
            </form>
        </div>
    </div>
</div>
@endsection
